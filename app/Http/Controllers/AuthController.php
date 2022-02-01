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
        
        if (isset($userData['author_id'])) {
            return abort(404);
        }
        if (isset($request['registration'])) {
            return redirect('registration');
        }
        
        $info['h1'] = 'Авторизация';

        if (isset($request['enter'])) {
            if ($user->authentication($request['login'], md5($request['password'])) == 'authenticationGO') {
                setcookie('login', md5($request['login'].md5($request['password'])));
                return redirect('/');
            }
            else {
                $info['h1'] = 'Проверьте логин и пароль';
            }
        }

        return view('auth', ['info' => $info]);
    }
}
