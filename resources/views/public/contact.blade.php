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

                <form class="contact-form" action="{{ route('contact.store') }}" method="POST" novalidate>
                    @csrf
                    <div class="honeypot" aria-hidden="true"><label for="website">Website</label><input id="website" type="text" name="website" value="" tabindex="-1" autocomplete="off"></div>
                    <div class="contact-name-grid">
                        <div><label for="first_name">First name</label><input id="first_name" class="form-control @error('first_name') is-invalid @enderror" type="text" name="first_name" value="{{ old('first_name') }}" placeholder="First name" required>@error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div><label for="last_name">Last name</label><input id="last_name" class="form-control @error('last_name') is-invalid @enderror" type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Last name" required>@error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    </div>
                    <div><label for="email">Email</label><input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" placeholder="you@company.com" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div><label for="phone">Phone number</label><div class="input-group"><span class="input-group-text">ID +62</span><input id="phone" class="form-control @error('phone') is-invalid @enderror" type="tel" name="phone" value="{{ old('phone') }}" placeholder="811 0000 0000">@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
                    <div><label for="message">Message</label><textarea id="message" class="form-control @error('message') is-invalid @enderror" name="message" rows="5" placeholder="Leave us a message..." required>{{ old('message') }}</textarea>@error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <button class="btn contact-submit" type="submit">Send Message</button>
                </form>
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
