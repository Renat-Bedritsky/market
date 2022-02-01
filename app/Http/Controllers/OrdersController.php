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
        $orders = new Order;

        $userData = $user->checkCookieLogin();

        if (!isset($userData['position']) || $userData['position'] == 'moderator' || $userData['position'] == 'user' || $userData['position'] == 'banned') {
            return abort(404);
        }
        
        $info['userData'] = $userData;

        $newOrders = $orders->getNewOrders();
        if (!empty($newOrders)) {
            foreach ($newOrders as $key => $path) {
                $newOrders[$key]['products'] = (array)json_decode($path['products']);
            }
        }

        $doneOrders = $orders->getDoneOrders($userData['login']);
        if (!empty($doneOrders)) {
            foreach ($doneOrders as $key => $path) {
                $doneOrders[$key]['products'] = (array)json_decode($path['products']);
            }
            $doneOrders = $doneOrders->reverse();
        }

        if (isset($request['order_done'])) {
            $orders->doneOrder($userData['login'], $request['order_done']);
            return redirect('orders');
        }

        if (isset($request['order_canceled'])) {
            $orders->canceledOrder($userData['login'], $request['order_canceled']);
            return redirect('orders');
        }

        $info['new_orders'] = $newOrders;
        $info['done_orders'] = $doneOrders;

        return view('orders', ['info' => $info]);
    }
}
