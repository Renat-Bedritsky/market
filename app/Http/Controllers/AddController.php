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
        $products = new Product;
        $functions = new Functions;

        $userData = $user->checkCookieLogin();
        $info['userData'] = $userData;

        if (!isset($userData['position']) || $userData['position'] == 'moderator' || $userData['position'] == 'user' || $userData['position'] == 'banned') {
            return abort(404);
        }

        $allCategories = Category::select('name', 'code')->get();
        $info['categories'] = $allCategories;

        if (isset($request['enter'])) {
            $infoForAdd = [];
            $infoForAdd += ['name' => $request['name']];
            $infoForAdd += ['description' => $request['description']];
            $infoForAdd += ['category_code' => $request['category_code']];
            $infoForAdd += ['price' => $request['price']];
            $infoForAdd += ['userData' => $userData];
            $data = $functions->loadProduct($infoForAdd);
            $products->setProduct($data);
            return redirect('add');
        }

        return view('add', ['info' => $info]);
    }
}
