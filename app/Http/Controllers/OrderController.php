<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Support\Functions;

class OrderController extends Controller
{
    function order(Request $request)
    {
        $user = new User;
        $product = new Product;
        $order = new Order;
        $functions = new Functions;

        $info['h2'] = 'Оформление заказа';

        $userData = $user->checkCookieLogin();
        $info['userData'] = $userData;
        
        $basket = $user->getBasket($userData['author_id']);
        $price = $product->totalPriceBasket($basket);
        if ($price == 0) {
            return redirect('basket');
        }
        $info['price'] = $price;

        $json = User::select('basket')->where('id', $userData['author_id'])->get();
        $info['basket'] = $json[0]['basket'];

        if (isset($request['order']) && isset($request['name']) && isset($request['phone']) && isset($request['email'])) {
            if (preg_match("/^[a-zA-Zа-яА-ЯёЁ]*$/u", $request['name'])) {
                if (preg_match("/^(\+375|80)(29|25|44|33)(\d{3})(\d{2})(\d{2})*$/u", $request['phone']))  {
                    if (preg_match("/^(|(([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\w+\-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6})$/i", $request['email']))  {
                        $order->addOrder($userData['author_id'], $request['name'], $request['phone'], $request['email'], $info['basket'], $info['price']);
                        User::where("id", $userData['author_id'])->update(["basket" => "[]"]);
                        header('Refresh: 5');
                    }
                    else {
                        $info['h2'] = 'Некоректный email';
                    }
                }
                else {
                    $info['h2'] = 'Некоректный телефон';
                }
            }
            else {
                $info['h2'] = 'Некоректное имя';
            }
        }

        if (isset($request['order']) && $info['h2'] == 'Оформление заказа') {
            $info['message'] = $functions->messageOrder();
        }
        else {
            $info['message'] = '';
        }

        return view('order', ['info' => $info]);
    }
}
