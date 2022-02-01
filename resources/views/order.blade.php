@extends('layout')

@section('title')Оформление заказа@endsection

@section('main_content')

<?= $info['message'] ?>

<div class="order">
    <div class="width">
        <div class="order_wrapper">
            <h2>{{ $info['h2'] }}</h2>
            <p>Общая стоимость: <span class="color_price">{{ $info['price'] }} BYN</span></p>
            <p>Укажите свои имя и номер телефона, чтобы наш менеджер мог с вами связаться:</p>

            <form method="POST">
                @csrf
                <table>
                    <tr>
                        <td>Имя</td>
                        <td><input type="text" name="name" required minlength="3"></td>
                    </tr>

                    <tr>
                        <td>Номер талефона</td>
                        <td><input type="text" name="phone" required minlength="3"></td>
                    </tr>
                    
                    <tr>
                        <td>Email</td>
                        <td><input type="email" name="email" required minlength="3"></td>
                    </tr>
                    
                    <tr>
                        <td></td>
                        <td><button name="order" class="order_basket">Заказать</button></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>

@endsection