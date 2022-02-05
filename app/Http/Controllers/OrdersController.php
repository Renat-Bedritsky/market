<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;

class OrdersController extends Controller
{
    function orders(Request $request)
    {
        $user = new User;

        $userData = $user->checkCookieLogin();
        $this->accessToThisPage($userData);

        $info['userData'] = $userData;
        $info['new_orders'] = $this->newOrders();
        $info['done_orders'] = $this->doneOrders($userData['login']);

        if (isset($request['order_done']) || isset($request['order_canceled'])) {
            $this->processingDoneOrder($request, $userData['login']);
            $this->processingCanceledOrder($request, $userData['login']);
            return redirect('orders');
        }

        return view('orders', ['info' => $info]);
    }

    function accessToThisPage($userData)
    {
        if (!isset($userData['position']) || $userData['position'] == 'moderator' || $userData['position'] == 'user' || $userData['position'] == 'banned') {
            return abort(404);
        }
    }

    function newOrders()
    {
        $orders = new Order;
        $newOrders = $orders->getNewOrders();
        if (!empty($newOrders)) {
            foreach ($newOrders as $key => $path) {
                $newOrders[$key]['products'] = (array)json_decode($path['products']);
            }
        }
        return $newOrders; 
    }

    function doneOrders($operator)
    {
        $orders = new Order;
        $doneOrders = $orders->getDoneOrders($operator);
        if (!empty($doneOrders)) {
            foreach ($doneOrders as $key => $path) {
                $doneOrders[$key]['products'] = (array)json_decode($path['products']);
            }
            $doneOrders = $doneOrders->reverse();
        }
        return $doneOrders;
    }

    function processingDoneOrder($request, $login)
    {
        $orders = new Order;
        if (isset($request['order_done'])) {
            $orders->doneOrder($login, $request['order_done']);
        }  
    }

    function processingCanceledOrder($request, $login)
    {
        $orders = new Order;
        if (isset($request['order_canceled'])) {
            $orders->canceledOrder($login, $request['order_canceled']);
        }  
    }
}