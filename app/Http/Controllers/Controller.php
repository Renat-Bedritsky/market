<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $user;

    function __construct()
    {
        date_default_timezone_set('Europe/Minsk');

        $uri = $_SERVER['REQUEST_URI'];
        
        $routes = [
            '0' => '\/?',
            '1' => '\/?([0-9]*)',
            '2' => '\?([a-z0-9=\&]*)',
            '3' => '\/?([0-9])*\?([a-z0-9=\&]*)',
            '4' => 'categories',
            '5' => 'basket',
            '6' => 'basket\/clear',
            '7' => '(mobile|portable|appliances|other)\/?',
            '8' => '(mobile|portable|appliances|other)\/([0-9]*)',
            '9' => '(mobile|portable|appliances|other)\/([0-9])*\?([a-z0-9=\&]*)',
            '10' => 'profile\/([a-zA-Zа-яА-ЯёЁ0-9]*)',
            '11' => 'detail\/([a-zA-Zа-яА-ЯёЁ0-9_-]*)',
            '12' => 'control\/([a-zA-Zа-яА-ЯёЁ0-9_-]*)',
            '13' => 'auth',
            '14' => 'logout',
            '15' => 'registration',
            '16' => 'order',
            '17' => 'add',
            '18' => 'orders'
        ];

        $error = true;

        foreach ($routes as $link) {
            if (preg_match("/^\/($link)$/u", $uri)) {
                $error = false;
                break;
            }
            else {
                continue;
            }
        }

        if ($error == true) {
            return abort(404);
        }
    }

    function defineMin($price)
    {
        if ($price != '') {
            if (is_numeric($price)) {
                return $price;
            }
            else {
                return abort(404);
            }
        }
        else {
            return 0;
        }
    }

    function defineMax($price)
    {
        if ($price != '') {
            if (is_numeric($price)) {
                return $price;
            }
            else {
                return abort(404);
            }
        }
        else {
            return 10000000;
        }
    }

    function defineNew($new)
    {
        if ($new != '') {
            if ($new == 'yes') {
                return date('Y-m-d H:i:s', time()-(7*24*60*60));
            }
            else {
                return abort(404);
            }
        }
        else {
            return '2000-01-01 01:01:01';
        }
    }

    function definelink($min, $max, $new)
    {
        $link = '';
        if ($min != 0) {
            $link = '?min='.$min;
        }
        else if ($min == 0 && $max != 10000000) {
            $link = '?min=';
        }
        if ($max != 10000000) {
            $link = $link.'&max='.$max;
        }
        else if ($min != 0 && $max == 10000000) {
            $link = $link.'&max=';
        }
        if ($new != '2000-01-01 01:01:01' && $min != 0) {
            $link = $link.'&new=yes';
        }
        else if ($new != '2000-01-01 01:01:01') {
            $link = '?new=yes';
        }
        return $link;
    }

    function requestPlusBasket($productCode)
    {
        $this->user = new User;
        $userData = $this->user->checkCookieLogin();

        $jsonBasket = $this->user->getBasket($userData['author_id']);
        $basket = (array)json_decode($jsonBasket[0]['basket']);
        $this->user->plusBasket($basket, $productCode, $userData['author_id']);
        return redirect($_SERVER['REQUEST_URI']);
    }
}
