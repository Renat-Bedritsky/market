@extends('layout')

@section('title')Корзина@endsection

@section('main_content')

<div class="basket">
    <div class="width">
        <h2>Корзина</h2>
        <p>Оформление заказа</p>

        <table class="basket_table">
            <tr>
                <td>Фото</td>
                <td>Название</td>
                <td>Количество</td>
                <td>Цена</td>
                <td>Стоимость</td>
            </tr>

            @foreach ($info['products'] as $product)
                <tr>
                    <td><img src="images/foto_products/{{ $product['image'] }}"></td>
                    <td><a href="detail/{{ $product['code'] }}">{{ $product['name'] }}</a></td>
                    <td>
                        <p>{{ $product['count'] }}</p>
                        <form method="POST">
                            @csrf
                            <button name="minus" value="{{ $product['code'] }}">-</button>
                            <button name="plus" value="{{ $product['code'] }}">+</button>
                        </form>
                    </td>
                    <td>{{ $product['price'] }} BYN</td>
                    <td>{{ $product['count'] * $product['price'] }} BYN</td>
                </tr>
            @endforeach

            <tr style="height:50px;">
                <td colspan="2">Итоговая стоимость:</td>
                <td colspan="2"></td>
                <td>{{ $info['total'] }} BYN</td>
            </tr>
        </table>

        <div class="basket_order">
            @if ($info['total'] > 0)
                <a href="/order">Оформить заказ</a>
            @endif
        </div>

        <div class="basket_clear">
            @if ($info['total'] > 0)
                <a href="/basket/clear">Очистить</a>
            @endif
        </div>
    </div>
</div>

@endsection