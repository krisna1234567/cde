@extends('layouts.public')

@section('content')
    <section class="services-page-section">
        <div class="container-xl">
            <x-public.breadcrumb :items="[
                ['label' => 'Product And Service', 'url' => route('products.index')],
                ['label' => 'Our Service', 'url' => null],
            ]" />

            <header class="services-page-heading">
                <h1>Built to Power Your Project at Every Stage</h1>
                <p>Whether you're just starting out or maintaining an existing system, we provide the expertise to keep your energy solutions running efficiently.</p>
            </header>

            <div class="services-page-grid">
                @forelse ($services as $service)
                    <article class="services-page-card">
                        <span class="services-page-icon"><i class="bi {{ $service['icon'] }}"></i></span>
                        <h2>{{ $service['title'] }}</h2>
                        <p>{{ $service['description'] }}</p>
                    </article>
                @empty
                    <p class="text-center text-muted grid-column-full">No active services are available yet.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
