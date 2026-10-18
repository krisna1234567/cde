@extends('layouts.public')

@section('content')
    <section class="catalog-page-section">
        <div class="container-xl">
            <x-public.breadcrumb :items="[
                ['label' => 'Product & Service', 'url' => route('products.index')],
                ['label' => 'Our Solar Products', 'url' => null],
            ]" />
            <header class="solutions-heading">
                <h1>Our Solar Products</h1>
                <p>Discover reliable and high-efficiency solar solutions designed to maximize the performance of your solar energy system.</p>
            </header>
            <form class="catalog-search" action="{{ route('products.catalog') }}" method="GET" role="search">
                @if($category !== '')<input type="hidden" name="category" value="{{ $category }}">@endif
                <button type="submit" aria-label="Search products"><i class="bi bi-search" aria-hidden="true"></i></button>
                <label class="visually-hidden" for="product-search">Search products</label>
                <input id="product-search" type="search" name="q" value="{{ $search }}" maxlength="150" placeholder="Search products...">
            </form>
            <nav class="catalog-categories" aria-label="Product categories">
                <a class="catalog-category {{ $category === '' ? 'is-active' : '' }}" href="{{ route('products.catalog', array_filter(['q' => $search])) }}" @if($category === '') aria-current="page" @endif>All</a>
                @foreach ($categories as $item)
                    <a class="catalog-category {{ $category === $item->name ? 'is-active' : '' }}" href="{{ route('products.catalog', array_filter(['category' => $item->name, 'q' => $search])) }}" @if($category === $item->name) aria-current="page" @endif>{{ $item->name }}</a>
                @endforeach
            </nav>
            <div class="product-grid">
                @forelse ($products as $product)
                    <x-public.product-card :product="$product" />
                @empty
                    <div class="catalog-empty grid-column-full">
                        <p>No products found{{ $search !== '' ? ' for “'.$search.'”' : '' }}.</p>
                        <a href="{{ route('products.catalog') }}">View all products</a>
                    </div>
                @endforelse
            </div>
            <div class="mt-5">{{ $products->links() }}</div>
        </div>
    </section>
@endsection
