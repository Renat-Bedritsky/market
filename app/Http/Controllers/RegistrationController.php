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
        
        if (!empty($userData)) {
            return abort(404);
        }
        
        $info['h1'] = 'Регистрация';

        if (isset($request['enter'])) {
            if ($request['password_1'] != $request['password_2']) {
                $info['h1'] = 'Пароли не совпадают';
            }
            else {
                $check = $user->checkLogin($request['login']);
                if ($check == 'User exist') {
                    $info['h1'] = 'Пользователь существует';
                }
                else if ($check == 'User not exist') {
                    $login = $request['login'];
                    $password = md5($request['password_1']);
                    $user->registrationUser($login, $password);
                    setcookie('login', md5($login.$password));
                    return redirect('/');
                }
            }
        }

        return view('registration', ['info' => $info]);
    }
}
