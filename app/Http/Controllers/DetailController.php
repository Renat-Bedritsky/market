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
        $userData = $user->checkCookieLogin();

        $infoProduct = $this->getInfoOfTheProduct($code);
        $infoProduct = $this->getCommentsOfTheProduct($infoProduct, $code, $userData);
        $infoProduct['userData'] = $userData;

        if (isset($request['plus']) && !empty($userData)) {
            $this->requestPlusBasket($request['plus']);
        }
        else if (isset($request['plus']) && empty($userData)) {
            return redirect('auth');
        }

        if (isset($request['enter_comment']) || isset($request['enter_update']) || isset($request['delete_comment_yes'])) {
            $this->processingAddComment($request, $code, $userData);
            $this->processingUpdateComment($request, $userData);
            $this->processingDeleteComment($request);
            return redirect('detail/'.$code);
        }

        return view('detail', ['info' => $infoProduct]);
    }

    function getInfoOfTheProduct($code)
    {
        $user = new User;
        $product = new Product;

        $infoProduct = $product->infoProduct($code);
        if (!sizeof($infoProduct)) {
            return abort(404);
        }

        $authorOfProduct = $user->authorOfProduct($infoProduct[0]['author_id']);
        $infoProduct[0]['author'] = $authorOfProduct[0]['login'];
        unset($infoProduct[0]['author_id']);

        return $infoProduct;
    }

    function getCommentsOfTheProduct($infoProduct, $code, $userData)
    {
        $user = new User;
        $functions = new Functions;
        $comment = new Comment;

        $singleProductComments = $comment->singleProductComments($code);
        if (empty($singleProductComments)) {
            $infoProduct['comments'] = '';
        }
        else {
            $ar = $user->getUsers($singleProductComments);
            $infoProduct['comments'] = $ar;
            foreach ($infoProduct['comments'] as $key => $comment) {
                $resolution = $functions->resolutionDelete($comment['user'], $userData);
                $infoProduct['comments'][$key]['user']['resolution'] = $resolution;
            }
        }
        return $infoProduct;
    }

    function processingAddComment($request, $code, $userData)
    {
        $comment = new Comment;
        if(isset($request['enter_comment'])) {
            $newComment = [
                'author_id' => $userData['author_id'],
                'product_code' => $code,
                'content' => $request['content']
            ];
            $comment->addComment($newComment);
        } 
    }

    function processingUpdateComment($request, $userData)
    {
        $comment = new Comment;
        if (isset($request['enter_update'])) {
            $comment->updateComment($request['content'], $userData['author_id'], $request['date']);
        }
    }

    function processingDeleteComment($request)
    {
        $comment = new Comment;
        if (isset($request['delete_comment_yes'])) {
            $comment->deleteComment($request['delete_comment_yes']);
        }
    }
}