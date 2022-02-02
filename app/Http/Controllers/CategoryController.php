<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{
    function getCategory(Request $request, $page)
    {
        $user = new User;
        $products = new Product;

        $m = explode('/', $_SERVER['REQUEST_URI']);
        $category = $m[1];
        if ($category != 'mobile' && $category != 'portable' && $category != 'appliances' && $category != 'other') {
            abort(404);
        }

        $info = $products->filterProducts($category, $page);
        $userData = $user->checkCookieLogin();

        if (isset($request['plus']) && !empty($userData)) {
            $jsonBasket = $user->getBasket($userData['author_id']);
            $basket = (array)json_decode($jsonBasket[0]['basket']);
            $user->plusBasket($basket, $request['plus'], $userData['author_id']);
            return redirect($_SERVER['REQUEST_URI']);
        }
        if (isset($request['plus']) && empty($userData)) {
            return redirect('autorization');
        }

        $info['userData'] = $userData;

        $mass = Category::all()->where('code', $category)->first();
        $info['category'] = $mass;
        $info['page'] = $page;

        if (isset($_GET['new'])) {
            if ($_GET['new'] == 'yes') {
                $new = '&new='.$_GET['new'];
            }
            else {
                return abort(404);
            }
        }
        else {
            $new = '';
        }

        if (isset($_GET['min']) || isset($_GET['max'])) {   // TO DO
            if (($page < 1 || $page > $info['pages']) && sizeof($info['products'])) {
                return redirect('/'.$category.'/1?min='.$_GET['min'].'&max='.$_GET['max'].$new);
            }
        }

        if ($page < 1 || $page > $info['pages']) {
            return abort(404);
        }
        
        return view('category', ['info' => $info]);
    }
}
