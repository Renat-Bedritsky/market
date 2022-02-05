<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ControlController extends Controller
{
    function control(Request $request, $login) {
        $user = new User;
        $userData = $user->checkCookieLogin();
        $info['userData'] = $userData;

        $this->accessToThisPage($userData);
        $this->checkingUserBeingViewed($login);

        if(isset($request['position'])) {
            $user->updatePosition($request['login'], $request['position']);
            return redirect('control/'.$login);
        }

        $names = $user->getNameAndPosition($userData['position']);
        $info['names'] = $names;
        $info['focus'] = $login;

        return view('control', ['info' => $info]);
    }

    function accessToThisPage($userData)
    {
        if (!isset($userData['position']) || $userData['position'] == 'operator' || $userData['position'] == 'user' || $userData['position'] == 'banned') {
            return abort(404);
        }
    }

    function checkingUserBeingViewed($login)
    {
        $user = new User;
        
        $check = $user->checkLogin($login);
        if (!sizeof($check)) {
            return abort(404);
        }
    }
}