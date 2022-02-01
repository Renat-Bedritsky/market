<?php $user = $info['userData']; ?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="shortcut icon" href="../images/site-images/mobile.jpg" type="image/png">
    <link href="{{ asset('css/style1.css') }}" rel="stylesheet">
    <link href="{{ asset('css/media1.css') }}" rel="stylesheet">
</head>

<body>

<div class="window">
    <div class="window_wrapper">
        <div class="header">
            <div class="header_wrapper width">
                <div>
                    <a href="/"><img src="../images/site-images/market.png" class="header_logo" alt="logo"></a>
                </div>

                <div class="header_nav">
			        <button class="header_nav_toggle"><label for="header_nav_toggle"></label></button>
				    <input type="checkbox" id="header_nav_toggle">
                    <ul>
                        <li><a href="/1">Товары</a></li>
                        <li><a href="/categories">Категории</a></li>
                        <li><a href="/basket">Корзина</a></li>
                    </ul>
                </div>

                @if (isset($user['login']) && $user['access'] == 'allowed')
                    <div class="enter_account">
                        <div class="nav_account">
                            <ul>
                                <li>
                                    <a>{{ $user['login'] }}</a>
                                    <ul>
                                        <li><a href="/profile/{{ $user['login'] }}">Профиль</a></li>
                                        @if ($user['position'] == 'operator' || $user['position'] == 'administrator')
                                            <li><a href="/add">Добавить</a></li>
                                            <li><a href="/orders">Заказы</a></li>
                                        @endif
                                        <li><a href="/logout">Выход</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                @else
                    <div class="enter_account">
                        <div><a href="/auth">Войти</a></div>
                    </div>
                @endif

            </div>
        </div>

        @yield('main_content')

        
    </div>

<footer class="footer">
    <div class="footer_wrapper width">
        <a href="index.php"><img src="../images/site-images/market.png" class="header_logo" alt="logo"></a>

        <ul>
            <li>59 Street, Newyork City, Rose Town, 05 Rive House</li>
            <li>+123 456 7890</li>
            <li>info@example.com</li>
        </ul>
    </div>

</footer>

</div>

</body>
</html>