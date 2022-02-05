<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    function auth(Request $request)
    {
        $user = new User;
        $userData = $user->checkCookieLogin();
        $info['userData'] = $userData;
        
        $this->accessToThisPage($userData);

        if (isset($request['registration'])) {
            return redirect('registration');
        }
        
        $info['h1'] = $this->titleAndCheckData($request);

        if ($info['h1'] == 'Авторизация пройдена') {
            return redirect('/');
        }

        return view('auth', ['info' => $info]);
    }

    function accessToThisPage($userData)
    {
        if (isset($userData['position'])) {
            return abort(404);
        }
    }

    function titleAndCheckData($request)
    {
        if (isset($request['enter'])) {
            return $this->checkLoginAndPassword($request);
        }
        else {
            return 'Авторизация';
        }
    }

    function checkLoginAndPassword($request)
    {
        $user = new User;

        if ($user->authentication($request['login'], md5($request['password'])) == 'authenticationGO') {
            setcookie('login', md5($request['login'].md5($request['password'])));
            return 'Авторизация пройдена';
        }
        else {
            return 'Проверьте логин и пароль';
        }
    }
}