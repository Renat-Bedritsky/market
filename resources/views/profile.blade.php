@extends('layout')

@section('title'){{ $info['focusData']['login'] }}@endsection

@section('main_content')

@if (isset($_POST['load_foto']))
    <style>body {overflow: hidden;} .update_foto {display: block;}</style>
    <div class="update_foto">
        <div class="update_foto_wrapper">
            <form method="POST" name="add_product" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" required><br>
                <button name="update_foto">Загрузить</button>
                <a href="/profile/{{ $info['userData']['login'] }}">Отмена</a>
            </form>
        </div>
    </div>
@endif

@if (isset($_POST['delete_product']))
    <style>body {overflow: hidden;} .delete_product {display: block;}</style>
    <div class="delete_product">
        <div class="delete_product_wrapper">
            <form method="POST">
                @csrf
                <p>Удалить товар?</p>
                <button name="delete_product_yes" value="{{ $_POST['delete_product'] }}">Удалить</button>
                <a href="/profile/{{ $info['userData']['login'] }}">Отмена</a>
            </form>
        </div>
    </div>
@endif

@if (isset($_POST['delete_comment']))
    <style>body {overflow: hidden;} .delete_field {display: block;}</style>
    <div class="delete_field">
        <div class="delete_field_wrapper">
            <form method="POST">
                @csrf
                <p>Удалить комментарий?</p>
                <button name="delete_comment_yes" value="{{ $_POST['delete_comment'] }}">Удалить</button>
                <a href="/profile/{{ $info['userData']['login'] }}">Отмена</a>
            </form>
        </div>
    </div>
@endif

<div class="profile">
    <div class="width">
        <h2>Административная панель</h2>

        <div class="profile_nav">
            <a href="/profile/{{ $info['userData']['login'] }}">Профиль</a>

            @if ($info['userData']['position'] == 'administrator' || $info['userData']['position'] == 'moderator')
                <a href="/control/{{ $info['focusData']['login'] }}">Управление</a>
            @endif
        </div>

        <div class="profile_wrapper">
            <div class="information" >
                <div class="profile">
                    <div class="profile_foto">
                        <img src="/images/foto_profiles/{{ $info['focusData']['foto'] }}" alt="foto">

                        @if ($info['focusData']['login'] == $info['userData']['login'])
                            <form method="POST">
                                @csrf
                                <input type="submit" name="load_foto" value="Загрузить фото">
                            </form>
                        @endif

                    </div>

                    <div class="profile_info">
                        <table>
                            <tr>
                                <td>Логин</td>
                                <td>{{ $info['focusData']['login'] }}</td>
                            </tr>

                            <tr>
                                <td>Доступ</td>
                                <td>{{ $info['focusData']['position'] }}</td>
                            </tr>

                            <tr>
                                <td>Количество товаров</td>
                                <td>{{ count($info['products']) }}</td>
                            </tr>

                            <tr>
                                <td>Количество коментариев</td>
                                <td>{{ count($info['comments']) }}</td>
                            </tr>

                            <tr>
                                <td>Дата регистрации</td>
                                <td>{{ $info['focusData']['created_at'] }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="search_user">
                    <form method="POST">
                        Найти пользователя<br>
                        @csrf
                        <input type="text" name="search_user" minlength="1" required>
                        <button>Поиск</button>
                    </form>

                    @if (isset($info['search']) && $info['search'] != '')
                        @foreach ($info['search'] as $value)
                            <p><a href="/profile/{{ $value['login'] }}">{{ $value['login'] }}</a></p>
                        @endforeach
                    @endif

                </div>

                <div class="profile_products">
                    <h3>Товары пользователя</h3>

                    <table>
                        @foreach ($info['products'] as $product)
                            <tr>
                                <td><a href="/detail/{{ $product['code'] }}">{{ $product['name'] }}</a></td>

                                <td>
                                    @if ($info['resolution'] == 'YES')
                                        <form method="POST">
                                            @csrf
                                            <button name="delete_product" value="{{ $product['code'] }}">Удалить</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                <div class="profile_comments">
                    <h3>Коментарии пользователя</h3>
                    <table>
                        @foreach ($info['comments'] as $comment)
                            <tr>
                                <td>
                                    <a href="/detail/{{ $comment['product_code'] }}">{{ $comment['content'] }}</a>
                                </td>

                                <td>
                                    @if ($info['resolution'] == 'YES')
                                        <form method="POST">
                                            @csrf
                                            <button name="delete_comment" value="{{ $comment['updated_at'] }}">Удалить</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection