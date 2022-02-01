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

        // Удаление товара
        if (isset($request['delete_product_yes'])) {
            Product::where('code', $request['delete_product_yes'])->delete();
            Comment::where('product_code', $request['delete_product_yes'])->delete();
            return redirect('profile/'.$login);
        }

        // Удаление коментария
        if (isset($request['delete_comment_yes'])) {
            Comment::where('updated_at', $request['delete_comment_yes'])->delete();
            return redirect('profile/'.$login);
        }

        $userData = $user->checkCookieLogin();
        $info['userData'] = $userData;

        $data = User::select('id', 'login', 'foto', 'basket', 'position', 'created_at')->where('login', $login)->get();
        if (!sizeof($data)) {
            return abort(404);
        }

        // Обновление фото профиля
        if (isset($request['update_foto'])) {
            if (file_exists($_SERVER['DOCUMENT_ROOT'].'/images/foto_profiles/'.$data[0]['foto']) && $data[0]['foto'] != '../site-images/start-foto.png') {
                unlink($_SERVER['DOCUMENT_ROOT'].'/images/foto_profiles/'.$data[0]['foto']);
            }
            $image = $functions->loadFoto('foto_profiles');
            $id = $userData['author_id'];
            User::where('id', $id)->update(['foto' => $image]);
            return redirect('profile/'.$login);
        }

        $info['focusData'] = $data[0];
        $arComments = Comment::select('product_code', 'content', 'updated_at')->where('author_id', $data[0]['id'])->get();
        $info['comments'] = $arComments;
        $arProducts = Product::select('id', 'name', 'code')->where('author_id', $data[0]['id'])->get();
        $info['products'] = $arProducts;

        $info['resolution'] = $functions->resolutionDelete($data[0], $userData);

        $link = $_SERVER['DOCUMENT_ROOT'].'/images/foto_profiles/'.$data[0]['foto'];
        if (!file_exists($link) || $data[0]['foto'] == '') {
            $data[0]['foto'] = '../site-images/start-foto.png';
        }

        if (isset($request['search_user'])) {
            $search = $request['search_user'];
            $result = User::select('login')->where('login', 'LIKE', $search.'%')->get();
            $info['search'] = $result;
        }

        return view('profile', ['info' => $info]);
    }
}
