@extends('layout')

@section('title'){{ $info['focus'] }}@endsection

@section('main_content')

@if (isset($_POST['update_position']))
    <style>body {overflow: hidden;} .update_position {display: block;}</style>
    <div class="update_position">
        <div class="update_position_wrapper">
            {{ $_POST['update_position'] }}
            <form method="POST">
                @csrf
                <input type="hidden" name="login" value="{{ $_POST['update_position'] }}">
                <select name="position">
                    <option value="user">Пользователь</option>
                    @if ($info['userData']['position'] == 'administrator')
                        <option value="moderator">Модератор</option>
                        <option value="operator">Оператор</option>
                    @endif
                    <option value="banned">Бан</option>
                </select><br>
                <input type="submit" name="enter" value="Изменить">
                <a href="/control/{{ $info['focus'] }}">Отмена</a>
            </form>
        </div>
    </div>
@endif

<div class="control">
    <div class="width">
        <h2>Административная панель</h2>

        <div class="control_nav">
                <a href="/profile/{{ $info['focus'] }}">Профиль</a>
                <a href="/control/{{ $info['focus'] }}">Управление</a>
        </div>

        <div class="control_wrapper">
            <div class="information" >
                <div class="users">
                    <table>
                        <tr>
                            <td>Логин</td>
                            <td>Статус</td>
                            <td>Править</td>
                        </tr>

                        @foreach ($info['names'] as $value)
                            <tr>
                                <td><a href="/profile/{{ $value['login'] }}">{{ $value['login'] }}</a></td>
                                <td><?= $value['position'] ?></td>
                                <td>
                                    <form method="POST">
                                        @csrf
                                        <button name="update_position" value="{{ $value['login'] }}">Изменить</button>
                                    </form>
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