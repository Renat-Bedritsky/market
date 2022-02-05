<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Support\Functions;

class AddController extends Controller
{
    function add(Request $request)
    {
        $user = new User;
        $category = new Category;

        $userData = $user->checkCookieLogin();
        $info['userData'] = $userData;

        $this->accessToThisPage($userData);

        $allCategories = $category->nameAndCodeCategories();
        $info['categories'] = $allCategories;

        if (isset($request['enter'])) {
            $this->actionAdd($request, $userData);
        }

        return view('add', ['info' => $info]);
    }

    function accessToThisPage($userData)
    {
        if (!isset($userData['position']) || $userData['position'] == 'moderator' || $userData['position'] == 'user' || $userData['position'] == 'banned') {
            return abort(404);
        }
    }

    function actionAdd($request, $userData)
    {
        $functions = new Functions;
        $products = new Product;
        
        $infoForAdd = [];
        $infoForAdd += ['name' => $request['name']];
        $infoForAdd += ['description' => $request['description']];
        $infoForAdd += ['category_code' => $request['category_code']];
        $infoForAdd += ['price' => $request['price']];
        $infoForAdd += ['userData' => $userData];
        $data = $functions->dataForLoadProduct($infoForAdd);
        $products->addProduct($data);
        return redirect('add');
    }
}