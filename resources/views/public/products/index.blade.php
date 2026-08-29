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
                    <figure class="services-image-wrap">
                        <img src="{{ asset($servicesSection['image']) }}" alt="{{ $servicesSection['image_alt'] }}" loading="lazy" width="760" height="500">
                    </figure>
                    <div class="services-content">
                        <h2>{{ $servicesSection['title'] }}</h2>
                        <p class="services-lead">{{ $servicesSection['subtitle'] }}</p>
                        <div class="services-overview-stats" aria-label="Service summary">
                            <article>
                                <strong>{{ number_format($servicesSummary['count']) }}</strong>
                                <span>Core Services</span>
                            </article>
                            <article>
                                <strong>{{ $servicesSummary['scope'] }}</strong>
                                <span>{{ $servicesSummary['scope_label'] }}</span>
                            </article>
                        </div>
                        <a class="services-overview-button" href="{{ route('products.services') }}">
                            <span>View All Services</span>
                            <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
