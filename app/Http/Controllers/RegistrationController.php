<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class RegistrationController extends Controller
{
    function registration(Request $request)
    {
        $user = new User;
        $userData = $user->checkCookieLogin();
        $info['userData'] = $userData;

        $this->accessToThisPage($userData);

        $info['h1'] = $this->titleAndCheckData($request);

        if ($info['h1'] == 'Пользователь зарегестрирован') {
            return redirect('/');
        }

        return view('registration', ['info' => $info]);
    }

    function accessToThisPage($userData)
    {
        if (!empty($userData)) {
            return abort(404);
        }
    }

    function titleAndCheckData($request)
    {
        if (isset($request['enter'])) {
            return $this->checkingTheEnteredPassword($request);
        }
        else {
            return 'Регистрация';
        }
    }

    function checkingTheEnteredPassword($request)
    {
        if ($request['password_1'] == $request['password_2']) {
            return $this->checkingTheEnteredLogin($request);
        }
        else {
            return 'Пароли не совпадают';
        }
    }

    function checkingTheEnteredLogin($request)
    {
        $user = new User;

        $check = $user->checkLogin($request['login']);
        if (sizeof($check)) {
            return 'Пользователь существует';
        }
        else {
            $login = $request['login'];
            $password = md5($request['password_1']);
            $user->registrationUser($login, $password);
            setcookie('login', md5($login.$password));
            return 'Пользователь зарегестрирован';
        }
    }
}