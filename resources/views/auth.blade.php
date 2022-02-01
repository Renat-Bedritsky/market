@extends('layout')

@section('title')Авторизация@endsection

@section('main_content')

<div class="auth_title">
    {{ $info['h1'] }}
</div>

<div class="auth">
    <form method="POST" action="auth">
        @csrf
        Логин<br>
        <input type="text" name="login" value=""><br>
        Пароль<br>
        <input type="password" name="password" value=""><br>
        <button name="enter">Войти</button>
        <button name="registration">Регистрация</button>
    </form>
</div>

@endsection