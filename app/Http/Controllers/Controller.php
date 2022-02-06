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

    function __construct()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $routes = $this->availableRoutes();
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

    private function availableRoutes()
    {
        return [
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
    }

    protected function checkCookieLogin()
    {
        return $this->processingCheckCookie();
    }

    private function processingCheckCookie()
    {
        $users = new User;
        $listUsers = $users->forCheckCookie();

        foreach($listUsers as $user) {
            if (isset($_COOKIE['login']) && $_COOKIE['login'] == md5($user['login'].$user['password'])) {
                $access = 'allowed';
                $userData = ['author_id' => $user['id'], 'login' => $user['login'], 'access' => $access, 'position' => $user['position']];
                return $userData;
            }
        }
    }

    protected function checkAuthData($login, $password)
    {
        return $this->processingCheckAuth($login, $password);
    }

    private function processingCheckAuth($login, $password)
    {
        $user = new User;

        $checkUser = $user->forCheckAuth($login);
        if (sizeof($checkUser)) {
            if ($login == $checkUser[0]['login'] && $password == $checkUser[0]['password']) {
                return 'loggedIn';
            }
        }
    }

    protected function defineMin($price)
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

    protected function defineMax($price)
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

    protected function defineNew($new)
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

    protected function definelink($min, $max, $new)
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

    protected function requestPlusBasket($productCode)
    {
        $user = new User;
        $userData = $this->checkCookieLogin();

        $jsonBasket = $user->getBasket($userData['author_id']);
        $basket = (array)json_decode($jsonBasket[0]['basket']);

        $this->processingPlusBasket($basket, $productCode, $userData['author_id']);
        return redirect($_SERVER['REQUEST_URI']);
    }

    protected function processingPlusBasket($basket, $productCode, $userId)
    {
        $user = new User;
        if (array_key_exists($productCode, $basket)) {
            $basket[$productCode] += 1;
        }
        else {
            if (array_key_exists(0, $basket)) {
                unset($basket[0]);
            }
            $count = 1;
            $basket += [$productCode => $count];
        }
        $json = json_encode($basket);
        $user->plusBasket($userId, $json);
    }

    protected function processingMinusBasket($basket, $productCode, $userId)
    {
        $user = new User;
        $basket[$productCode] -= 1;
        if ($basket[$productCode] == 0) {
            unset($basket[$productCode]);
        }
        $json = json_encode($basket);
        $user->minusBasket($userId, $json);
    }
}