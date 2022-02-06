<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Support\Functions;

class AddController extends Controller
{
    protected function add(Request $request)
    {
        $category = new Category;
        $userData = $this->checkCookieLogin();
        $this->accessToThisPage($userData);

        $info['userData'] = $userData;

        $allCategories = $category->nameAndCodeCategories();
        $info['categories'] = $allCategories;

        if (isset($request['enter'])) {
            $this->actionAdd($request, $userData);
            return redirect('add');
        }

        return view('add', ['info' => $info]);
    }

    private function accessToThisPage($userData)
    {
        if (!isset($userData['position']) || $userData['position'] == 'moderator' || $userData['position'] == 'user' || $userData['position'] == 'banned') {
            return abort(404);
        }
    }

    private function actionAdd($request, $userData)
    {
        $functions = new Functions;
        $products = new Product;
        
        $infoForAdd = [];
        $infoForAdd += ['name' => $request['name']];
        $infoForAdd += ['description' => $request['description']];
        $infoForAdd += ['category_code' => $request['category_code']];
        $infoForAdd += ['price' => $request['price']];
        $infoForAdd += ['userData' => $userData];
        
        $dataForLoadProduct = $functions->dataForLoadProduct($infoForAdd);
        $products->addProduct($dataForLoadProduct);
    }
}