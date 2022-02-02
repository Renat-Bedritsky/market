<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;

class BaseController extends Controller
{
    function getProducts(Request $request, $page = 1)
    {
        $user = new User;
        $products = new Product;
        $userData = $user->checkCookieLogin();
        $get = '';

        if (isset($request['min']) && $request['min'] != '') {
            if (is_numeric($request['min'])) {
                $min = $request['min'];
                $get = '?min='.$min;
            }
            else {
                return abort(404);
            }
        }
        else {
            $min = 0;
        }

        if (isset($request['max']) && $request['max'] != '') {
            if (is_numeric($request['max'])) {
                $max = $request['max'];
                $get = $get.'&max='.$max;
            }
            else {
                return abort(404);
            }
        }
        else if (isset($request['min'])) {
            $max = 10000000;
            $get = $get.'&max=';
        }
        else {
            $max = 10000000;
        }

        if (isset($request['new']) && $request['new'] == 'yes') {
            $new = date('Y-m-d H:i:s', time()-(7*24*60*60));
            $get = $get.'&new=yes';
        }
        else if (isset($request['min'])) {
            $new = '2000-01-01 01:01:01';
            $get = $get.'&new=no';
        }
        else {
            $new = '2000-01-01 01:01:01';
        }

        $filterProducts = $products->filterProductsForBase($min, $max, $new, $page);
        $countProducts = $products->countProductsForBase($min, $max, $new);

        $pages = ceil($countProducts / PRODUCTS_ON_PAGE);
        $info['products'] = $filterProducts;
        $info['pages'] = $pages;
        $info['get'] = $get;

        if (isset($request['plus'])) {
            if (!empty($userData)) {
                $jsonBasket = $user->getBasket($userData['author_id']);
                $basket = (array)json_decode($jsonBasket[0]['basket']);
                $user->plusBasket($basket, $request['plus'], $userData['author_id']);
                return redirect($_SERVER['REQUEST_URI']);
            }
            else {
                return redirect('autorization');
            }
        }

        $info['userData'] = $userData;

        if ($page < 1 || $page > $info['pages']) {
            return redirect('/1'.$get);
        }

        return view('base', ['info' => $info]);
    }

    function logout()
    {
        setcookie('login', $_COOKIE['login'], time()-10);
        return redirect('/');
    }
}
