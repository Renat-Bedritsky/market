<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{
    protected function getCategory(Request $request, $page = 1)
    {
        $products = new Product;
        $categories = new Category;
        $userData = $this->checkCookieLogin();
        $info['userData'] = $userData;

        $minPrice = $this->defineMin($request['min']);
        $maxPrice = $this->defineMax($request['max']);
        $newProduct = $this->defineNew($request['new']);
        $codeCategory = $this->defineCategory();
        $link = $this->definelink($minPrice, $maxPrice, $newProduct);

        $filterProducts = $products->filterProductsForCategory($minPrice, $maxPrice, $newProduct, $codeCategory, $page);
        $countProducts = $products->countProductsForCategory($minPrice, $maxPrice, $newProduct, $codeCategory);

        $pages = ceil($countProducts / PRODUCTS_ON_PAGE);
        $info['products'] = $filterProducts;
        $info['pages'] = $pages;
        $info['link'] = $link;

        if (isset($request['plus']) && !empty($userData)) {
            $this->requestPlusBasket($request['plus']);
        }
        else if (isset($request['plus']) && empty($userData)) {
            return redirect('auth');
        }

        $infoCategory = $categories->infoCategory($codeCategory);
        $info['category'] = $infoCategory[0];

        if (($page < 1 || $page > $info['pages']) && $pages != 0) {
            return redirect($codeCategory.'/1'.$link);
        }
        
        return view('category', ['info' => $info]);
    }

    private function defineCategory()
    {
        $partLink = explode('/', $_SERVER['REQUEST_URI']);
        $codeCategory = $partLink[1];
        if ($codeCategory == 'mobile' || $codeCategory == 'portable' || $codeCategory == 'appliances' || $codeCategory == 'other') {
            return $codeCategory;
        }
        else {
            return abort(404);
        }
    }
}