<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;

class BasketController extends Controller
{
    function getBasket(Request $request)
    {
        $user = new User;
        $products = new Product;

        $userData = $user->checkCookieLogin();
        if (empty($userData['author_id'])) {
            return redirect('auth');
        }
        $info['userData'] = $userData;

        $jsonBasket = $user->getBasket($userData['author_id']);
        $basket = (array)json_decode($jsonBasket[0]['basket']);

        if (isset($request['plus']) && !empty($userData)) {
            $user->plusBasket($basket, $request['plus'], $userData['author_id']);
            return redirect('basket');
        }
        if (isset($request['minus']) && !empty($userData)) {
            $user->minusBasket($basket, $request['minus'], $userData['author_id']);
            return redirect('basket');
        }

        // echo '<pre>';
        // print_r($basket);
        // echo '</pre>';

        // $info = $products->productForBasketv1($basket);
        $info['products'] = [];
        $total = 0;
        foreach ($basket as $product => $count) {
            $infoProduct = $products->infoProduct($product);
            // if (empty($infoProduct)) {
            //     unset($basket[$product]);
            //     $user->updateBasket($basket, $request['userData']['author_id']);
            //     return redirect('basket');
            // }
            $infoProduct['count'] = $count;
            $total += $infoProduct['price'] * $count;
            array_push($info['products'], $infoProduct);
        }
        $info += ['total' => $total];

        // echo '<pre>';
        // print_r($info);
        // echo '</pre>';

        return view('basket', ['info' => $info]);
    }

    function clearBasket()
    {
        $user = new User;
        $userData = $user->checkCookieLogin();
        User::where("id", $userData['author_id'])->update(["basket" => "[]"]);
        return redirect('basket');
    }
}
