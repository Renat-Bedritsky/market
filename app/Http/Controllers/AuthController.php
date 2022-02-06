<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    protected function auth(Request $request)
    {
        $userData = $this->checkCookieLogin();
        $this->accessToThisPage($userData);

        $info['userData'] = $userData;

        if (isset($request['registration'])) {
            return redirect('registration');
        }
        
        $info['h1'] = $this->titleAndCheckData($request);

        if ($info['h1'] == 'Авторизация пройдена') {
            return redirect('/');
        }

        return view('auth', ['info' => $info]);
    }

    private function accessToThisPage($userData)
    {
        if (isset($userData['position'])) {
            return abort(404);
        }
    }

    private function titleAndCheckData($request)
    {
        if (isset($request['enter'])) {
            return $this->checkLoginAndPassword($request);
        }
        else {
            return 'Авторизация';
        }
    }

    private function checkLoginAndPassword($request)
    {
        $checkAuthData = $this->checkAuthData($request['login'], md5($request['password']));
        if ($checkAuthData == 'loggedIn') {
            setcookie('login', md5($request['login'].md5($request['password'])));
            return 'Авторизация пройдена';
        }
        else {
            return 'Проверьте логин и пароль';
        }
    }
}