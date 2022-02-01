@extends('layout')

@section('title')Заказы@endsection

@section('main_content')

@if (isset($_POST['view_order']))
    <style>body {overflow: hidden;} .view_order {display: block;}</style>
    <div class="view_order">
        <div class="view_order_window">
            <form method="POST">
                @csrf
                <button name="order_done" value="{{ $_POST['view_order'] }}">Выполнен</button><br>
                <button name="order_canceled" value="{{ $_POST['view_order'] }}">Отменить</button><br>
                <a href="/orders">Закрыть</a>
            </form>
        </div>
    </div>
@endif

<div class="orders">
    <div class="width">
        <h2>Заказы</h2>

        <div class="orders_wrapper">
            <div class="orders_new">
                <div class="orders_new_wrapper">
                    <h3>Не выполненые</h3>

                    @if (isset($info['new_orders']) && sizeof($info['new_orders']))
                        @foreach ($info['new_orders'] as $order)
                            <table>
                                <tr><td>{{ $order['name'] }}</td></tr>

                                <tr><td>{{ $order['phone'] }}</td></tr>

                                <tr><td>{{ $order['email'] }}</td></tr>

                                @foreach ($order['products'] as $product => $count)
                                    <tr class="orders_product_name"><td>{{ $product }}</td></tr>
                                    <tr class="orders_product_count"><td>Количество: {{ $count }}</td></tr>
                                @endforeach

                                <tr><td>Итого: {{ $order['price'] }}</td></tr>

                                <tr><td>{{ $order['created_at'] }}</td></tr>

                                <tr>
                                    <td>
                                        <form method=POST>
                                            @csrf
                                            <button name="view_order" value="{{ $order['id'] }}">Оформить</button>
                                        </form>
                                    </td>
                                </tr>
                            </table>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="orders_done">
                <div class="orders_done_wrapper">
                    <h3>Выполненые</h3>

                    @if (isset($info['done_orders']) && sizeof($info['done_orders']))
                        @foreach ($info['done_orders'] as $order)
                            <table>
                                <tr><td>{{ $order['name'] }}</td></tr>

                                <tr><td>{{ $order['phone'] }}</td></tr>

                                <tr><td>{{ $order['email'] }}</td></tr>

                                @foreach ($order['products'] as $product => $count)
                                    <tr class="orders_product_name"><td>{{ $product }}</td></tr>
                                    <tr class="orders_product_count"><td>Количество: {{ $count }}</td></tr>
                                @endforeach

                                <tr><td>Итого: {{ $order['price'] }}</td></tr>

                                <tr><td>{{ $order['updated_at'] }}</td></tr>

                                <tr><td><b>{{ $order['status'] }}</b></td></tr>
                            </table>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection