@extends('layout')

@section('title')Главная@endsection

@section('main_content')

<div class="products">
    <div class="wrapper">
        <h2>Товары</h2>

        <div class="products_filter width">
            <form>
                Цена от 
                <input type="number" name="min" value="<?php if (isset($_GET['min']) && $_GET['min'] != 0) echo $_GET['min'] ?>">
                до
                <input type="number" name="max" value="<?php if (isset($_GET['max']) && $_GET['max'] != 10000000) echo $_GET['max'] ?>">
                <input type="checkbox" name="new" value="yes"> Новинка
                <button type="submit" name="" value="">Фильтр</button>
                <a href="/">Сброс</a>
            </form>
        </div>
        
        <div class="products_wrapper width">
            @if (!sizeof($info['products']))
                <h2 style="margin: 50px auto;">Товаров нет</h2>
            @endif

            @foreach ($info['products'] as $product)
                <div class="product">
                    <div class="product_wrapper">
                        <img src="images/foto_products/{{ $product['image'] }}" alt="{{ $product['code'] }}">
                        <p>{{ $product['name'] }}</p>
                        <p>{{ $product['price'] }} BYN</p>
                                
                        <form method="POST">
                            @csrf
                            <button name="plus" value="{{ $product['code'] }}">В корзину</button>
                            <a href="/detail/{{ $product['code'] }}">Подробнее</a>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Форма для пагинации -->
    <div class="pages_pagination width">
        Страница:
        @for ($i = 1; $i <= $info['pages']; $i++)
            <span>
                <a href="{{ '/'.$i.$info['link'] }}">{{ $i }}</a>
            </span>
        @endfor
    </div>
</div>

@endsection