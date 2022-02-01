<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    public $timestamps = false;

    function filterProducts($category, $page)
    {
        date_default_timezone_set('Europe/Minsk');
        if (isset($_GET['min']) || isset($_GET['max'])) {
            if (isset($_GET['new']) && $_GET['new'] == 'yes') {
                $new = date('Y-m-d H:i:s', time()-(7*24*60*60));
                $get = '?min='.$_GET['min'].'&max='.$_GET['max'].'&new='.$_GET['new'];
            }
            else {
                $new = '2000-01-01 01:01:01';
                $get = '?min='.$_GET['min'].'&max='.$_GET['max'];
            }
        }
        else {
            $new = '2000-01-01 01:01:01';
            $get = '';
        }

        if (!isset($_GET['min']) || $_GET['min'] == '') $min = 0;
        else $min = $_GET['min'];

        if (!isset($_GET['max']) || $_GET['max'] == '') $max = 10000000;
        else $max = $_GET['max'];

        if (empty($category)) {
            $mass = $this->all()->reverse()->where('price', '>=', $min)->where('price', '<=', $max)->where('created_at', '>', $new)->forPage($page, ONPAGE);
            $count = $this->all()->where('price', '>=', $min)->where('price', '<=', $max)->where('created_at', '>', $new)->count();
        }
        else {
            $mass = $this->all()->reverse()->where('price', '>=', $min)->where('price', '<=', $max)->where('created_at', '>', $new)->where('category_code', '=', $category)->forPage($page, ONPAGE);
            $count = $this->all()->where('price', '>=', $min)->where('price', '<=', $max)->where('created_at', '>', $new)->where('category_code', '=', $category)->count();
        }

        $pages = ceil($count / ONPAGE);

        $array['products'] = $mass;
        $array['pages'] = $pages;
        $array['get'] = $get;

        return $array;
    }


    // Функция для страницы basket
    function productsForBasket($array)
    {
        $products = [];
        $basket = [];
        $total = 0;
        foreach ($array as $code => $count) {
            $product = Product::all()->where('code', $code);
            foreach ($product as $path) {
                $product = $path;
            }
            if(empty($product)) $product['price'] = 0;
            $product['count'] = $count;
            array_push($products, $product);
            $total += $product['price'] * $count;
        }
        $basket += ['products' => $products];
        $basket += ['total' => $total];
        return $basket;
    }
    

    // Сумма цены корзины
    function totalPriceBasket($basket)
    {
        $total = 0;
        foreach ($basket as $key => $path) {
            $price = Product::select('price')->where('code', $key)->get();
            if(!isset($price[0]['price'])) continue;
            $sum = $price[0]['price'] * $path;
            $total += $sum;
        }
       return $total;
    }


    // Функция для добавления товара
    function setProduct($data)
    {
        date_default_timezone_set('Europe/Minsk');

        Product::insert([
            'category_code' => $data['category_code'],
            'author_id' => $data['author_id'],
            'name' => $data['name'],
            'code' => $data['code'],
            'description' => $data['description'],
            'image' => $data['image'],
            'price' => $data['price'],
            'created_at' => date("Y-m-d H:i:s")
        ]);
    }
}
