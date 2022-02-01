<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function __construct()
    {
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
}
