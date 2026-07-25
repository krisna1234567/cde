@extends('layouts.public')

@section('content')
    <x-public.page-hero :title="$hero['title']" :description="$hero['description']" :image="$hero['image']" :image-alt="$hero['image_alt']" />

    <section class="product-list-section">
        <div class="container-xl">
            <div class="inner-section-heading text-center">
                <h2>{{ $productsSection['title'] }}</h2>
                @if ($productsSection['subtitle'])<p>{{ $productsSection['subtitle'] }}</p>@endif
            </div>
            <div class="product-grid">
                @forelse ($products as $product)
                    <x-public.product-card :product="$product" />
                @empty
                    <p class="text-center text-muted grid-column-full">No active products are available yet.</p>
                @endforelse
            </div>
        </div>
    </section>

    @if ($servicesSection['enabled'])
        <section class="services-overview-section">
            <div class="container-xl">
                <div class="services-overview-grid">
                    <div class="services-image-wrap"><img src="{{ asset($servicesSection['image']) }}" alt="{{ $servicesSection['image_alt'] }}" loading="lazy" width="760" height="920"></div>
                    <div class="services-content">
                        <h2>{{ $servicesSection['title'] }}</h2>
                        <p class="services-lead">{{ $servicesSection['subtitle'] }}</p>
                        <div class="services-list-grid">
                            @foreach ($services as $service)
                                <article class="service-item"><span><i class="bi {{ $service['icon'] }}"></i></span><h3>{{ $service['title'] }}</h3><p>{{ $service['description'] }}</p></article>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
