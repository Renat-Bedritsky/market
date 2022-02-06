<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;

class CategoriesController extends Controller
{
    protected function getCategories()
    {
        $category = new Category;
        $userData = $this->checkCookieLogin();

        $info['categories'] = $category->infoCategories();
        $info['userData'] = $userData;

        return view('categories', ['info' => $info]);
    }
}