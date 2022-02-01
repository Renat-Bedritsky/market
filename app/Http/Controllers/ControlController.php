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

        if (!isset($userData['position']) || $userData['position'] == 'operator' || $userData['position'] == 'user' || $userData['position'] == 'banned') {
            return abort(404);
        }
        
        $check = User::select('login')->where('login', $login)->get();
        if (!sizeof($check)) {
            return abort(404);
        }

        if(isset($request['position'])) {
            User::where('login', $request['login'])->update(['position'=> $request['position']]);
            return redirect('control/'.$login);
        }

        $names = $user->getNameAndPosition($userData['position']);
        $info['names'] = $names;
        $info['focus'] = $login;

        return view('control', ['info' => $info]);
    }
}
