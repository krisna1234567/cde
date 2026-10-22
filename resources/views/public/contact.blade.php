@extends('layouts.public')

@section('content')
    <x-public.page-hero :title="$hero['title']" :description="$hero['description']" :image="$hero['image']" :image-alt="$hero['image_alt']" />

    <section class="contact-main-section">
        <div class="container-xl">
            @if (session('success'))
                <div class="alert alert-success contact-alert" role="alert">{{ session('success') }}</div>
            @endif

            <div class="contact-main-grid">
                <div class="contact-information">
                    <h2>{{ $contactSection['title'] }}</h2>
                    <p>{{ $contactSection['subtitle'] ?: $contactSection['content'] }}</p>
                    <address>
                        @if ($site['address'])<div><span><i class="bi bi-geo-alt"></i></span><p>{{ $site['address'] }}</p></div>@endif
                        @if ($site['phone'])<div><span><i class="bi bi-telephone"></i></span><p><a href="tel:{{ preg_replace('/\s+/', '', $site['phone']) }}">{{ $site['phone'] }}</a></p></div>@endif
                        @if ($site['email'])<div><span><i class="bi bi-envelope"></i></span><p><a href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a></p></div>@endif
                    </address>
                </div>

                @include('public.partials.contact-form', ['formAction' => route('contact.store')])
            </div>
        </div>
    </section>

    @if ($mapSection['enabled'] && $site['maps_embed_url'])
        <section class="map-section" aria-labelledby="mapTitle">
            <div class="container-xl">
                <div class="map-heading"><h2 id="mapTitle">{{ $mapSection['title'] }}</h2><p>{{ $site['address'] }}</p></div>
                <div class="map-frame"><iframe src="{{ $site['maps_embed_url'] }}" title="Location of {{ $site['name'] }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe></div>
            </div>
        </section>
    @endif
@endsection
