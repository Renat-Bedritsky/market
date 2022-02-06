<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Comment;
use App\Support\Functions;

class DetailController extends Controller
{
    protected function getDetail(Request $request, $code)
    {
        $userData = $this->checkCookieLogin();
        
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

    private function getInfoOfTheProduct($code)
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

    private function getCommentsOfTheProduct($infoProduct, $code, $userData)
    {
        $comment = new Comment;

        $singleProductComments = $comment->singleProductComments($code);
        if (empty($singleProductComments)) {
            $infoProduct['comments'] = '';
        }
        else {
            $comments = $this->getInformationAboutCommentators($singleProductComments, $userData);
            $infoProduct['comments'] = $comments;
        }
        return $infoProduct;
    }

    private function getInformationAboutCommentators($singleProductComments, $userData)
    {
        $user = new User;
        $functions = new Functions;

        foreach ($singleProductComments as $serialNumber => $comment) {
            $id = $comment['author_id'];
            $infoUser = $user->getUser($id);

            $infoUser[0]['foto'] = $this->photoCheck($infoUser[0]['foto']);
            $singleProductComments[$serialNumber]['user'] = $infoUser[0];
        }

        foreach ($singleProductComments as $serialNumber => $comment) {
            $resolution = $functions->resolutionDelete($comment['user'], $userData);
            $singleProductComments[$serialNumber]['user']['resolution'] = $resolution;
        }
        return $singleProductComments;
    }

    private function photoCheck($fotoName)
    {
        $way = $_SERVER['DOCUMENT_ROOT'].'/public/images/foto_profiles/'.$fotoName;

        if (!file_exists($way) || $fotoName == '') {
            return '../site-images/start-foto.png';
        }
        else {
            return $fotoName;
        }
    }

    private function processingAddComment($request, $code, $userData)
    {
        if(isset($request['enter_comment'])) {
            $comment = new Comment;
            
            $newComment = [
                'author_id' => $userData['author_id'],
                'product_code' => $code,
                'content' => $request['content']
            ];
            $comment->addComment($newComment);
        } 
    }

    private function processingUpdateComment($request, $userData)
    {
        if (isset($request['enter_update'])) {
            $comment = new Comment;
            $comment->updateComment($request['content'], $userData['author_id'], $request['date']);
        }
    }

    private function processingDeleteComment($request)
    {
        if (isset($request['delete_comment_yes'])) {
            $comment = new Comment;
            $comment->deleteComment($request['delete_comment_yes']);
        }
    }
}