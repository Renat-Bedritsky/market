<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Support\Functions;

class OrderController extends Controller
{
    protected function order(Request $request)
    {
        $user = new User;
        $userData = $this->checkCookieLogin();
        $info['userData'] = $userData;
        
        $jsonBasket = $user->getBasket($userData['author_id']);
        $basket = (array)json_decode($jsonBasket[0]['basket']);
        $basketPrice = $this->basketPrice($basket);
        if ($basketPrice == 0) {
            return redirect('basket');
        }
        $info['basket'] = $jsonBasket[0]['basket'];
        $info['basketPrice'] = $basketPrice;
        $info['h2'] = $this->titleAndCheckData($request, $userData, $info['basket'], $info['basketPrice']);
        $info['message'] = $this->message($request, $info['h2']);

        return view('order', ['info' => $info]);
    }

    private function basketPrice($basket)
    {
        $products = new Product;
        $total = 0;
        foreach ($basket as $product => $count) {
            $price = $products->priceProduct($product);
            $total += $price[0]['price'] * $count;
        }
        return $total;
    }

    private function titleAndCheckData($request, $userData, $basket, $basketPrice)
    {
        if (isset($request['order']) && isset($request['name']) && isset($request['phone']) && isset($request['email'])) {
            return $this->checkName($request, $userData, $basket, $basketPrice);
        }
        else {
            return 'Оформление заказа';
        }
    }

    private function checkName($request, $userData, $basket, $basketPrice)
    {
        if (preg_match("/^[a-zA-Zа-яА-ЯёЁ]*$/u", $request['name'])) {
            return $this->checkPhone($request, $userData, $basket, $basketPrice);
        }
        else {
            return 'Некоректное имя';
        }
    }

    private function checkPhone($request, $userData, $basket, $basketPrice)
    {
        if (preg_match("/^(\+375|80)(29|25|44|33)(\d{3})(\d{2})(\d{2})*$/u", $request['phone']))  {
            return $this->checkEmail($request, $userData, $basket, $basketPrice);
        }
        else {
            return 'Некоректный телефон';
        }
    }

    private function checkEmail($request, $userData, $basket, $basketPrice)
    {
        $order = new Order;
        $user = new User;
        if (preg_match("/^(|(([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\w+\-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6})$/i", $request['email']))  {
            $order->addOrder($userData['author_id'], $request['name'], $request['phone'], $request['email'], $basket, $basketPrice);
            $user->clearBasket($userData['author_id']);
            header('Refresh: 5');
            return 'Оформление заказа';
        }
        else {
            return 'Некоректный email';
        }
    }

    private function message($request, $title)
    {
        $functions = new Functions;
        if (isset($request['order']) && $title == 'Оформление заказа') {
            return $functions->messageOrder();
        }
        else {
            return '';
        }
    }
}