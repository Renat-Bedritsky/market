<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    public $timestamps = false;

    function filterProductsForBase($min, $max, $new, $page)
    {
        return $this->all()->reverse()->where('price', '>=', $min)->where('price', '<=', $max)->where('created_at', '>', $new)->forPage($page, PRODUCTS_ON_PAGE);
    }

    function countProductsForBase($min, $max, $new)
    {
        return $this->all()->where('price', '>=', $min)->where('price', '<=', $max)->where('created_at', '>', $new)->count();
    }

    function filterProductsForCategory($min, $max, $new, $category, $page)
    {
        return $this->all()->reverse()->where('price', '>=', $min)->where('price', '<=', $max)->where('created_at', '>', $new)->where('category_code', '=', $category)->forPage($page, PRODUCTS_ON_PAGE);
    }

    function countProductsForCategory($min, $max, $new, $category)
    {
        return $this->all()->where('price', '>=', $min)->where('price', '<=', $max)->where('created_at', '>', $new)->where('category_code', '=', $category)->count();  
    }


    // Функция для страницы basket
    function infoProduct($code)
    {
        $product = Product::select('name', 'code', 'image', 'price')->where('code', '=', $code)->get();
        return $product[0];
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
    function addProduct($data)
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
