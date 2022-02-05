<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;

class CategoriesController extends Controller
{
    function getCategories()
    {
        $user = new User;
        $category = new Category;
        $userData = $user->checkCookieLogin();

        $info['categories'] = $category->infoCategories();
        $info['userData'] = $userData;

        return view('categories', ['info' => $info]);
    }
}