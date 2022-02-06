<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    function infoCategories()
    {
        return Category::select('name', 'code', 'description', 'image')->get();  
    }

    function nameAndCodeCategories()
    {
        return Category::select('name', 'code')->get();
    }

    function infoCategory($code)
    {
        return Category::select('name', 'code')->where('code', '=', $code)->get();
    }
}