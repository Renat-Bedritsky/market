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

    function forCheckCookie()
    {
        return User::select('id', 'login', 'password', 'position')->get();
    }

    function forCheckAuth($login)
    {
        return User::select('login', 'password')->where('login', '=', $login)->get();
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
        return User::select('id', 'login', 'foto', 'basket', 'position', 'created_at')->where('login', '=', $login)->get();
    }

    function updateFoto($id, $image)
    {
        User::where('id', $id)->update(['foto' => $image]);
    }

    function searchUser($login)
    {
        return User::select('login')->where('login', 'LIKE', $login.'%')->get();
    }

    function getUser($id)
    {
        return User::select('id', 'login', 'foto', 'position')->where('id', '=', $id)->get();
    }

    function getNameAndPosition()
    {
        return User::select('login', 'position')->get(); 
    }

    function clearBasket($userId)
    {
        User::where("id", $userId)->update(["basket" => "[]"]);
    }
}