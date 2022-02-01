<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;

class BaseController extends Controller
{
    function get(Request $request, $page = 1)
    {
        $user = new User;
        $products = new Product;

        $userData = $user->checkCookieLogin();

        if (isset($request['plus']) && !empty($userData)) {
            $basket = $user->getBasket($userData['author_id']);
            $user->plusBasket($basket, $request['plus'], $userData['author_id']);
            return redirect($_SERVER['REQUEST_URI']);
        }
        if (isset($request['plus']) && empty($userData)) {
            return redirect('autorization');
        }

        $info = $products->filterProducts('', $page);
        $info['userData'] = $userData;

        if (isset($_GET['new'])) {
            if ($_GET['new'] == 'yes') {
                $new = '&new='.$_GET['new'];
            }
            else {
                return abort(404);
            }
        }
        else $new = '';

        if (isset($_GET['min']) || isset($_GET['max'])) {   // TO DO
            if (($page < 1 || $page > $info['pages']) && sizeof($info['products'])) {
                return redirect('/1?min='.$_GET['min'].'&max='.$_GET['max'].$new);
            }
        }

        if ($page < 1 || $page > $info['pages']) {
            return abort(404);
        }

        return view('base', ['info' => $info]);
    }

    function logout()
    {
        setcookie('login', $_COOKIE['login'], time()-10);
        return redirect('/');
    }
}
