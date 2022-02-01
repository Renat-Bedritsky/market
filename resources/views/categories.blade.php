@extends('layout')

@section('title')Категории@endsection

@section('main_content')

<div class="categories">
    <div class="width">
        <div class="categories_wrapper">
            @foreach ($info['categories'] as $category)
                <div class="category">
                    <img src="images/site-images/{{ $category['image'] }}">
                    <p><a href="{{ $category['code'] }}/1">{{ $category['name'] }}</a></p>
                    <p>{{ $category['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

@endsection