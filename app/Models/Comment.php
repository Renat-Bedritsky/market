<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;


    // Функция для добавления коментариев
    function addComment($data) {
        date_default_timezone_set('Europe/Minsk');

        Comment::insert([
            'author_id' => $data['author_id'],
            'product_code' => $data['product_code'],
            'content' => $data['content'],
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);
    }


    // Функция для удаления одного коментария к товару
    function deleteComment($date) {
        Comment::where('updated_at', $date)->delete();
    }


    // Функция для обновления комментария
    function updateComment($text, $author_id, $date) {
        date_default_timezone_set('Europe/Minsk');
        Comment::where('author_id', $author_id)->where('updated_at', $date)->update(['content'=> $text], ['updated_at' => date("Y-m-d H:i:s")]);
    }
}