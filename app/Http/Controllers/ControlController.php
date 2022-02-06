<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ControlController extends Controller
{
    protected function control(Request $request, $login) {
        $user = new User;
        $userData = $this->checkCookieLogin();
        $this->accessToThisPage($userData);
        $this->checkingUserBeingViewed($login);

        $info['userData'] = $userData;

        if(isset($request['position'])) {
            $user->updatePosition($request['login'], $request['position']);
            return redirect('control/'.$login);
        }

        $info['users'] = $this->getPositionUsers($userData['position']);
        $info['focus'] = $login;

        return view('control', ['info' => $info]);
    }

    private function accessToThisPage($userData)
    {
        if (!isset($userData['position']) || $userData['position'] == 'operator' || $userData['position'] == 'user' || $userData['position'] == 'banned') {
            return abort(404);
        }
    }

    private function checkingUserBeingViewed($login)
    {
        $user = new User;
        
        $check = $user->checkLogin($login);
        if (!sizeof($check)) {
            return abort(404);
        }
    }

    private function getPositionUsers($cookiePosition)
    {
        $user = new User;

        $users = $user->getNameAndPosition();
        $availableUsers = [];
        foreach ($users as $serialNumber => $infoUser) {
            if ($infoUser['position'] == 'administrator') continue;
            if ($infoUser['position'] == 'moderator' && $cookiePosition == 'moderator') continue;
            if ($infoUser['position'] == 'operator' && $cookiePosition == 'moderator') continue;
            array_push($availableUsers, $users[$serialNumber]);
        }
        return $availableUsers; 
    }
}