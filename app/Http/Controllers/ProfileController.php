<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Comment;
use App\Support\Functions;

class ProfileController extends Controller
{
    function profile(Request $request, $login)
    {
        $user = new User;
        $functions = new Functions;
        $product = new Product;
        $comment = new Comment;

        $userData = $user->checkCookieLogin();
        $info['userData'] = $userData;

        $profileInformation = $this->getProfileInformation($login);

        if (isset($request['delete_product_yes']) || $request['delete_comment_yes'] || isset($request['update_foto'])) {
            $this->processingDeleteProduct($request, $login);
            $this->processingDeleteComment($request, $login);
            $this->processingUpdateFoto($request, $profileInformation['foto'], $userData['author_id']);
            return redirect('profile/'.$login);
        }

        $info['focusData'] = $profileInformation;
        $info['comments'] = $comment->singleUserComments($profileInformation['id']);
        $info['products'] = $product->singleUserProducts($profileInformation['id']);
        $info['resolution'] = $functions->resolutionDelete($profileInformation, $userData);
        $info['search'] = $this->processingSearchUser($request);

        return view('profile', ['info' => $info]);
    }

    function processingDeleteProduct($request)
    {
        if (isset($request['delete_product_yes'])) {
            $product = new Product;
            $comment = new Comment;
            
            $product->deleteProduct($request['delete_product_yes']);
            $comment->removeCommentsOfOneProduct($request['delete_product_yes']);
        }
    }

    function processingDeleteComment($request)
    {
        if (isset($request['delete_comment_yes'])) {
            $comment = new Comment;
            $comment->deleteComment($request['delete_comment_yes']);
        } 
    }

    function getProfileInformation($login)
    {
        $user = new User;

        $profileInformation = $user->profileInformation($login);
        if (!sizeof($profileInformation)) {
            return abort(404);
        }

        $link = $_SERVER['DOCUMENT_ROOT'].'/images/foto_profiles/'.$profileInformation[0]['foto'];
        if (!file_exists($link) || $profileInformation[0]['foto'] == '') {
            $profileInformation[0]['foto'] = '../site-images/start-foto.png';
        }

        return $profileInformation[0];
    }

    function processingUpdateFoto($request, $foto, $userId)
    {
        if (isset($request['update_foto'])) {
            $user = new User;
            $functions = new Functions;
    
            if (file_exists($_SERVER['DOCUMENT_ROOT'].'/images/foto_profiles/'.$foto) && $foto != '../site-images/start-foto.png') {
                unlink($_SERVER['DOCUMENT_ROOT'].'/images/foto_profiles/'.$foto);
            }
            $image = $functions->loadFoto('foto_profiles');
            $user->updateFoto($userId, $image);
        }
    }

    function processingSearchUser($request)
    {
        if (isset($request['search_user'])) {
            $user = new User;
            return $user->searchUser($request['search_user']);
        }
    }
}