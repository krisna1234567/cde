@props(['product'])
<article class="product-card">
    <a href="{{ route('products.show', $product['slug']) }}" aria-label="View {{ $product['name'] }}">
        <span class="product-image-frame">
            <img src="{{ asset($product['image']) }}" alt="{{ $product['image_alt'] }}" loading="lazy" width="600" height="470">
        </span>
        <h3>{{ $product['name'] }}</h3>
    </a>
</article>
