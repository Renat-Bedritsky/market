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
        $info['userData'] = $userData;

        $minPrice = $this->defineMin($request['min']);
        $maxPrice = $this->defineMax($request['max']);
        $newProduct = $this->defineNew($request['new']);
        $link = $this->definelink($minPrice, $maxPrice, $newProduct);

        $filterProducts = $products->filterProductsForBase($minPrice, $maxPrice, $newProduct, $page);
        $countProducts = $products->countProductsForBase($minPrice, $maxPrice, $newProduct);

        $pages = ceil($countProducts / PRODUCTS_ON_PAGE);
        $info['products'] = $filterProducts;
        $info['pages'] = $pages;
        $info['link'] = $link;

        if (isset($request['plus']) && !empty($userData)) {
            $this->requestPlusBasket($request['plus']);
        }
        else if (isset($request['plus']) && empty($userData)) {
            return redirect('auth');
        }

        if (($page < 1 || $page > $info['pages']) && $pages != 0) {
            return redirect('/1'.$link);
        }

        return view('base', ['info' => $info]);
    }

    function logout()
    {
        setcookie('login', $_COOKIE['login'], time()-10);
        return redirect('/');
    }
}
