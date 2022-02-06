<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    public $timestamps = false;

    function addProduct($data)
    {
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

    function infoProduct($code)
    {
        return Product::select('id','category_code', 'author_id', 'name', 'code', 'description', 'image', 'price')->where('code', '=', $code)->get();
    }
    
    function priceProduct($code)
    {
        return Product::select('price')->where('code', '=', $code)->get();
    }

    function singleUserProducts($authorId)
    {
        return Product::select('id', 'name', 'code')->where('author_id', '=', $authorId)->get();
    }

    function deleteProduct($code)
    {
        Product::where('code', '=', $code)->delete(); 
    }
}