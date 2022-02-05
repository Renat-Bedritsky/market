<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    function addComment($data)
    {
        Comment::insert([
            'author_id' => $data['author_id'],
            'product_code' => $data['product_code'],
            'content' => $data['content'],
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);
    }

    function deleteComment($date)
    {
        Comment::where('updated_at', '=', $date)->delete();
    }

    function updateComment($text, $author_id, $date)
    {
        Comment::where('author_id', '=', $author_id)->where('updated_at', '=', $date)->update(['content'=> $text], ['updated_at' => date("Y-m-d H:i:s")]);
    }

    function singleProductComments($code)
    {
        return Comment::select('*')->where('product_code', '=', $code)->get();
    }

    function removeCommentsOfOneProduct($productCode)
    {
        Comment::where('product_code', '=', $productCode)->delete();
    }

    function singleUserComments($authorId)
    {
        return Comment::select('product_code', 'content', 'updated_at')->where('author_id', '=', $authorId)->get();
    }
}