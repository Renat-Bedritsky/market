<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Comment;
use App\Support\Functions;

class DetailController extends Controller
{
    function getDetail(Request $request, $code)
    {
        $user = new User;
        $functions = new Functions;
        $comment = new Comment;

        $info = Product::select('id','category_code', 'author_id', 'name', 'code', 'description', 'image', 'price')->where('code', $code)->get();
        if (!sizeof($info)) {
            abort(404);
        }
        
        $login = User::select('login')->where('id', $info[0]['author_id'])->get();
        $info[0]['author'] = $login[0]['login'];
        unset($info[0]['author_id']);

        $userData = $user->checkCookieLogin();
        $info['userData'] = $userData;

        $com = Comment::select('*')->where('product_code', $code)->get();
        if (empty($com)) {
            $info['comments'] = '';
        }
        else {
            $ar = $user->getUsers($com);
            $info['comments'] = $ar;
            foreach ($info['comments'] as $key => $comment) {
                $resolution = $functions->resolutionDelete($comment['user'], $userData);
                $info['comments'][$key]['user']['resolution'] = $resolution;
            }
        }

        if (isset($request['plus'])) {
            $basket = $user->getBasket($userData['author_id']);
            $user->plusBasket($basket, $request['plus'], $userData['author_id']);
            return redirect('detail/'.$code);
        }

        // Добавление коментария
        if(isset($request['enter_comment'])) {
            $newComment = [
                'author_id' => $userData['author_id'],
                'product_code' => $code,
                'content' => $request['content']
            ];
            $comment->addComment($newComment);
            return redirect('detail/'.$code);
        }

        // Изменение коментария
        if (isset($request['enter_update'])) {
            $comment->updateComment($request['content'], $userData['author_id'], $request['date']);
            return redirect('detail/'.$code);
        }

        // Удаление коментария
        if (isset($request['delete_comment_yes'])) {
            $comment->deleteComment($request['delete_comment_yes']);
            return redirect('detail/'.$code);
        }

        return view('detail', ['info' => $info]);
    }
}
