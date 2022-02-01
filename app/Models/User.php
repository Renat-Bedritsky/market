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

    // Функция для получения всех логинов и паролей (для авторизации) TO DO
    // protected function allLoginAndPass() {
    //     return $this->all();
    // }


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
            // else if (isset($_COOKIE['login'])) setcookie('login', $_COOKIE['login'], time()-10);  // Возможно не понадобится
        }
        return '0';
    }


    // Функция для получения данных корзины
    function getBasket($id)
    {
        $basket = User::all()->where('id', $id);
        foreach ($basket as $path) {
            $mass = json_decode($path['basket']);   // Декодировка
        }
        $result = (array)$mass;
        return $result;
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


    // Функция для добавления товара в корзину
    function plusBasket($array, $code, $user_id)
    {
        if (array_key_exists($code, $array)) {                       // Если товар есть в корзине
            $array[$code] += 1;
        }
        else {                                                       // Если товара нет в корзине
            if (array_key_exists(0, $array)) {
                unset($array[0]);
            }
            $count = 1;
            $array += [$code => $count];
        }
        $json = json_encode($array);
        User::where("id", $user_id)->update(["basket" => "$json"]);
    }


    // Функция для удаления товара из корзины
    function minusBasket($array, $code, $user_id)
    {
        $array[$code] -= 1;
        if ($array[$code] == 0) {
            unset($array[$code]);
        }
        $json = json_encode($array);
        User::where("id", $user_id)->update(["basket" => "$json"]);
    }


    // Обновить корзину
    function updateBasket($basket, $user_id)
    {
        $array = [];
        foreach ($basket as $path) {
            $array += [$path['code'] => $path['count']];

        }
        $json = json_encode($array);
        User::where("id", $user_id)->update(["basket" => "$json"]);
    }


    // Функция для проверки существования логина (для регистрации)
    function checkLogin($login)
    {
        $allUsers = User::select('login')->get();
        foreach ($allUsers as $user) {
            if ($user['login'] == $login) {
                return 'User exist';
            }
        }
        return 'User not exist';
    }

    
    // Функция для добавления пользователя
    function registrationUser($login, $password)
    {
        date_default_timezone_set('Europe/Minsk');
        $date = date("Y-m-d H:i:s");

        User::insert([
            'login' => $login,
            'password' => $password,
            'foto' => 'start-foto.png',
            'basket' => '[]',
            'position' => 'user',
            'created_at' => $date
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
}
