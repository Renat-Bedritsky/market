@extends('layout')

@section('title'){{ $info[0]['name'] }}@endsection

@section('main_content')

<?php $user = $info['userData']; ?>

@if (isset($_POST['delete_comment']))
    <style>body {overflow: hidden;} .delete_field {display: block;}</style>
    <div class="delete_field">
        <div class="delete_field_wrapper">
            <form method="POST">
                @csrf
                <p>Удалить комментарий?</p>
                <button name="delete_comment_yes" value="{{ $_POST['delete_comment'] }}">Удалить</button>
                <a href="/detail/{{ $info[0]['code'] }}">Отмена</a>
            </form>
        </div>
    </div>
@endif

@if (isset($_POST['update_comment'])) 
    <style>body {overflow: hidden;} .update_field {display: block;}</style>
    <div class="update_field">
        <div class="update_field_wrapper">
            <form method="POST">
                @csrf
                <textarea rows="10" cols="60" minlength="3" name="content">{{ $_POST['content'] }}</textarea><br>
                <input type="hidden" name="author_id" value="{{ $_POST['author_id'] }}">
                <input type="hidden" name="date" value="{{ $_POST['date'] }}">
                <input type="submit" name="enter_update" value="Изменить">
                <a href="/detail/{{ $info[0]['code'] }}">Отмена</a>
            </form>
        </div>
    </div>
@endif

<div class="detail">
    <div class="width">
        <div class="detail_wrapper">
            <h2>{{ $info[0]['name'] }}</h2>
            <img src="../images/foto_products/{{ $info[0]['image'] }}" alt="{{ $info[0]['code'] }}">
            <p>{{ $info[0]['description'] }}</p>
            <p>Цена: {{ $info[0]['price'] }} BYN</p>
            <form method="POST">
                @csrf
                @if (isset($user['login']) && $user['access'] == 'allowed')
                    <button name="plus" value="{{ $info[0]['code'] }}">Добавить в корзину</button>
                @else
                    <span class="add_basket_detail"><a href="/autorization">Добавить в корзину</a></span>
                @endif
            </form>
            <p>Автор: <a href="/profile/{{ $info[0]['author'] }}">{{ $info[0]['author'] }}</a></p>
        </div>
        
        <div class="detail_comments">
            Комментарии:
            @foreach ($info['comments'] as $comment)
                <div class="comment">
                    <table>
                        <tr><td rowspan="5"><img src="../images/foto_profiles/{{ $comment['user']['foto'] }}"></td></tr>

                        <tr>
                            <td><b><a href="/profile/{{ $comment['user']['login'] }}">{{ $comment['user']['login'] }}</a></b></td>
                            <td><b style="color: rgb(12, 81, 94);">{{ $comment['user']['position'] }}</b></td>
                        </tr>

                        <tr><td colspan="2">{{ $comment['content'] }}</td></tr>
                        
                        <tr>
                            <td>{{ $comment['updated_at'] }}</td>

                            <td>
                                @if ($comment['user']['resolution'] == 'YES')
                                    <form method="POST">
                                        @csrf
                                        <button class="delete_comment" name="delete_comment" value="{{ $comment['updated_at'] }}">Удалить</button>
                                    </form>
                                @endif

                                @if (!empty($user) && $user['author_id'] == $comment['author_id'] && $user['position'] != 'banned')
                                    <form method="POST">
                                        @csrf
                                        <input type="hidden" name="content" value="{{ $comment['content'] }}">
                                        <input type="hidden" name="author_id" value="{{ $comment['author_id'] }}">
                                        <input type="hidden" name="date" value="{{ $comment['updated_at'] }}">
                                        <button class="update_comment" name="update_comment">Изменить</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            @endforeach

            @if (!empty($user) && $user['access'] == 'allowed' && $user['position'] != 'banned')
                <form method="POST" class="detail_comment_add">
                    @csrf
                    <textarea rows="5" cols="60" minlength="3" name="content" value=""></textarea><br>
                    <button name="enter_comment">Отправить</button>
                </form>
            @endif
        </div>
    </div>
</div>

@endsection