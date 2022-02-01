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
            return redirect('autorization');
        }
        $basket = $user->getBasket($userData['author_id']);

        if (isset($request['plus']) && !empty($userData)) {
            $user->plusBasket($basket, $request['plus'], $userData['author_id']);
            return redirect('basket');
        }
        if (isset($request['minus']) && !empty($userData)) {
            $user->minusBasket($basket, $request['minus'], $userData['author_id']);
            return redirect('basket');
        }

        $info = $products->productsForBasket($basket);
        $info['userData'] = $userData;

        foreach ($info['products'] as $key => $path) {
            if ($path['price'] == 0) {
                unset($info['products'][$key]);
                $user->updateBasket($info['products'], $request['userData']['author_id']);
                return redirect('basket');
            }
        }

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
