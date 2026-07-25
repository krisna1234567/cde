@extends('layouts.public')

@section('content')
    <section class="detail-page-section product-detail-page">
        <div class="container-xl">
            <x-public.breadcrumb :items="[
                ['label' => 'Product & Service', 'url' => route('products.index')],
                ['label' => 'Product Details', 'url' => null],
            ]" />

            <div class="product-detail-grid">
                <div class="product-detail-image">
                    <img src="{{ asset($product['image']) }}" alt="{{ $product['image_alt'] }}" width="600" height="470">
                </div>
                <article class="product-detail-content">
                    <h1>{{ $product['name'] }}</h1>
                    @if ($product['brand'])<p class="product-brand">{{ $product['brand'] }}</p>@endif
                    <p class="product-price">
                        @if ($product['price'] !== null)
                            {{ $product['currency_symbol'] }}{{ number_format($product['price'], 2) }}
                        @else
                            Contact for Price
                        @endif
                    </p>
                    <hr>
                    @foreach ($product['description'] as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </article>
            </div>
        </div>
    </section>
@endsection
