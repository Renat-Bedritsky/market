<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public $timestamps = false;


    // Функция для сверки COOKIE с существующими пользователями
    function checkCookieLogin()
    {
        $listUsers = User::all();

        foreach($listUsers as $user) {
            if (isset($_COOKIE['login']) && $_COOKIE['login'] == md5($user['login'].$user['password'])) {
                $access = 'allowed';
                $userData = ['author_id' => $user['id'], 'login' => $user['login'], 'access' => $access, 'position' => $user['position']];
                return $userData;
            }
        }
        return '0';
    }


    // Функция для проверки логина и пароля (для авторизации)
    function authentication($login, $password)
    {
        $listUsers = User::all();
        foreach($listUsers as $user) {
            if ($login == $user['login'] && $password == $user['password']) {
                return 'authenticationGO';
            }
        }
    }

    function getBasket($id)
    {
        return User::select('basket')->where('id', '=', $id)->get();
    }

    function plusBasket($userId, $basket)
    {
        User::where("id", '=', $userId)->update(["basket" => "$basket"]);
    }

    function minusBasket($userId, $basket)
    {
        User::where("id", $userId)->update(["basket" => "$basket"]);
    }

    function checkLogin($login)
    {
        return User::select('login')->where('login', '=', $login)->get();
    }

    function updatePosition($userLogin, $newPosition)
    {
        User::where('login', '=', $userLogin)->update(['position'=> $newPosition]);
    }

    function authorOfProduct($userId)
    {
        return User::select('login')->where('id', '=', $userId)->get();
    }

    function profileInformation($login)
    {
        return User::select('id', 'login', 'foto', 'basket', 'position', 'created_at')->where('login', $login)->get();
    }

    function updateFoto($id, $image)
    {
        User::where('id', $id)->update(['foto' => $image]);
    }

    function searchUser($login)
    {
        return User::select('login')->where('login', 'LIKE', $login.'%')->get();
    }

    
    // Функция для добавления пользователя
    function registrationUser($login, $password)
    {
        User::insert([
            'login' => $login,
            'password' => $password,
            'foto' => 'start-foto.png',
            'basket' => '[]',
            'position' => 'user',
            'created_at' => date("Y-m-d H:i:s")
        ]);
    }


    // Для страницы detail
    function getUsers($data)
    {
        foreach ($data as $key => $path) {
            $id = $path['author_id'];
            $user = User::select('id', 'login', 'foto', 'position')->where('id', $id)->get();

            $link = $_SERVER['DOCUMENT_ROOT'].'/public/images/foto_profiles/'.$user[0]['foto'];
            if (!file_exists($link) || $user[0]['foto'] == '') {
                $user[0]['foto'] = '../site-images/start-foto.png';
            }

            $data[$key]['user'] = $user[0];
        }
        return $data;
    }


    // Функция для получения логинов и статусов пользователей (для смены статуса в control)
    function getNameAndPosition($access)
    {
        $data = User::select('login', 'position')->get();
        $result = [];
        foreach ($data as $key => $path) {
            if ($path['position'] == 'administrator') continue;
            if ($path['position'] == 'moderator' && $access == 'moderator') continue;
            if ($path['position'] == 'operator' && $access == 'moderator') continue;
            array_push($result, $data[$key]);
        }
        return $result;  
    }

    function clearBasket($userId)
    {
        User::where("id", $userId)->update(["basket" => "[]"]);
    }
}
