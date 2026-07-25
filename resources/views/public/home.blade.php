@extends('layouts.public')

@section('content')
    <section class="hero-section" style="background-image: linear-gradient(180deg, rgba(3, 20, 35, .43), rgba(3, 20, 35, .48)), url('{{ asset($hero['image']) }}');">
        <div class="container-xl hero-content">
            <h1>{{ $hero['title'] }}</h1>
            <p>{{ $hero['description'] }}</p>
            <div class="hero-actions">
                <a class="btn btn-hero-outline" href="{{ $hero['primary_button']['url'] }}">{{ $hero['primary_button']['label'] }}</a>
                <a class="btn btn-hero-primary" href="{{ $hero['secondary_button']['url'] }}">{{ $hero['secondary_button']['label'] }}</a>
            </div>
        </div>
    </section>

    @if ($calculator['enabled'])
    <section class="solar-calculator-section" aria-labelledby="solarCalculatorTitle">
        <div class="container-xl">
            <h2 id="solarCalculatorTitle" class="section-title text-center">{{ $calculator['title'] }}</h2>
            <div class="calculator-grid">
                <div class="calculator-image-wrap">
                    <img src="{{ asset($calculator['image']) }}" alt="{{ $calculator['image_alt'] }}" loading="lazy" width="650" height="520">
                </div>
                <form id="solarCalculator" class="solar-calculator-form" novalidate>
                    <div class="form-field">
                        <label for="monthlyBill">Monthly Electricity Bill (Rupiah)</label>
                        <input id="monthlyBill" class="form-control calculator-input" type="text" inputmode="numeric" value="{{ number_format($calculator['defaults']['monthly_bill'], 0, ',', '.') }}" autocomplete="off">
                    </div>
                    <div class="form-field">
                        <label for="installedPower">Installed Power (Watts)</label>
                        <input id="installedPower" class="form-control calculator-input" type="number" min="1" step="1" value="{{ $calculator['defaults']['installed_power'] }}">
                    </div>
                    <div class="form-field">
                        <label for="maximumCapacity">Maximum Installed Capacity (kWp)</label>
                        <input id="maximumCapacity" class="form-control calculator-output" type="text" value="{{ number_format($calculator['defaults']['maximum_capacity'], 1, ',', '.') }}" readonly>
                    </div>
                    <div class="form-field">
                        <label for="availableSpace">Available Space (m²)</label>
                        <input id="availableSpace" class="form-control calculator-output" type="text" value="{{ number_format($calculator['defaults']['available_space'], 0, ',', '.') }} m²" readonly>
                    </div>
                    <div class="form-field">
                        <label for="billSavings">Your Electricity Bill Savings</label>
                        <input id="billSavings" class="form-control calculator-output" type="text" value="{{ number_format($calculator['defaults']['bill_savings'], 0, ',', '.') }}" readonly>
                    </div>
                    <div class="form-field">
                        <label for="billWithSolar">Electricity Bill with Solar Panels</label>
                        <input id="billWithSolar" class="form-control calculator-output" type="text" value="{{ number_format($calculator['defaults']['bill_with_solar'], 0, ',', '.') }}" readonly>
                    </div>
                    <p class="calculator-note mb-0">Illustrative estimate only. A site survey is required for the final technical and financial calculation.</p>
                </form>
            </div>
        </div>
    </section>
    @endif

    @if ($company['enabled'])
    <section class="company-section">
        <div class="container-xl">
            <div class="company-heading-grid">
                <h2 class="section-title mb-0">{{ $company['title'] }}</h2>
                <p class="company-description mb-0"><strong>{{ $company['name'] }}</strong> {{ $company['description'] }}</p>
            </div>
            <div class="company-image-wrap">
                <img src="{{ asset($company['image']) }}" alt="{{ $company['image_alt'] }}" loading="lazy" width="1488" height="453">
            </div>

            @if ($impactSection['enabled'])
                <p class="impact-kicker">{{ $impactSection['title'] }}</p>
                <div class="impact-card">
                    @foreach ($impact as $item)
                        <div class="impact-item"><strong>{{ $item['value'] }}</strong><span>{{ $item['label'] }}</span></div>
                    @endforeach
                </div>
            @endif

            @if ($clientsSection['enabled'])
                <div class="clients-block">
                    <p>{{ $clientsSection['title'] }}</p>
                    <div class="client-logo-track" role="list" aria-label="Client logos">
                        @foreach ($clients as $client)
                            <div class="client-logo-item" role="listitem"><img src="{{ asset($client['logo']) }}" alt="{{ $client['image_alt'] }}" loading="lazy" width="180" height="72"></div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
    @endif

    @if ($benefitsSection['enabled'])
        <section class="solar-benefits-section">
            <div class="container-xl">
                <div class="section-heading-light text-center">
                    <h2>{{ $benefitsSection['title'] }}</h2>
                    <p>{{ $benefitsSection['subtitle'] }}</p>
                </div>
                <div class="benefits-grid">
                    @foreach ($benefits as $benefit)
                        <article class="benefit-card"><i class="bi {{ $benefit['icon'] }}" aria-hidden="true"></i><h3>{{ $benefit['title'] }}</h3><p>{{ $benefit['description'] }}</p></article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($hybrid['enabled'])
        <section class="hybrid-section">
            <div class="container-xl">
                <div class="hybrid-grid">
                    <div class="hybrid-diagram"><img src="{{ asset($hybrid['image']) }}" alt="{{ $hybrid['image_alt'] }}" loading="lazy" width="650" height="442"></div>
                    <div class="hybrid-content">
                        <h2 class="section-title">{{ $hybrid['title'] }}</h2>
                        <p class="hybrid-intro">{{ $hybrid['description'] }}</p>
                        <div class="hybrid-item-grid">
                            @foreach ($hybrid['items'] as $item)
                                <article class="hybrid-item"><span class="hybrid-icon"><i class="bi {{ $item['icon'] }}"></i></span><h3>{{ $item['title'] }}</h3><p>{{ $item['description'] }}</p></article>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if ($projectsSection['enabled'])
        <section class="projects-section">
            <div class="container-xl">
                <div class="section-heading text-center">
                    <h2>{{ $projectsSection['title'] }}</h2>
                    @if ($projectsSection['subtitle'])<p>{{ $projectsSection['subtitle'] }}</p>@endif
                </div>
                <div class="projects-grid">
                    @foreach ($projects as $project)
                        <x-public.project-card :project="$project" />
                    @endforeach
                </div>
                <div class="text-center"><a class="btn btn-see-more" href="{{ route('projects.index') }}">See more</a></div>
            </div>
        </section>
    @endif

    @if ($testimonialsSection['enabled'])
        <section class="testimonials-section">
            <div class="container-xl">
                <div class="section-heading text-center">
                    <h2>{{ $testimonialsSection['title'] }}</h2>
                    @if ($testimonialsSection['subtitle'])<p>{{ $testimonialsSection['subtitle'] }}</p>@endif
                </div>
                <div id="testimonialViewport" class="testimonial-viewport">
                    <div id="testimonialTrack" class="testimonial-track">
                        @foreach ($testimonials as $testimonial)
                            <article class="testimonial-card">
                                <div class="testimonial-profile"><div class="testimonial-avatar" aria-hidden="true">{{ $testimonial['initials'] }}</div><div><h3>{{ $testimonial['name'] }}</h3><p>{{ $testimonial['role'] }}</p></div></div>
                                <div class="testimonial-rating" aria-label="{{ $testimonial['rating'] }} out of 5 stars">@for ($i = 0; $i < $testimonial['rating']; $i++)<i class="bi bi-star-fill"></i>@endfor</div>
                                <blockquote>“{{ $testimonial['quote'] }}”</blockquote>
                            </article>
                        @endforeach
                    </div>
                </div>
                <div id="testimonialDots" class="testimonial-dots" aria-label="Testimonial slider navigation"></div>
            </div>
        </section>
    @endif

    @if ($contactCta['enabled'])
    <section class="contact-cta-section" style="background-image: linear-gradient(90deg, rgba(10, 69, 104, .72), rgba(10, 69, 104, .33)), url('{{ asset($contactCta['image']) }}');">
        <div class="container-xl text-center">
            <h2>{{ $contactCta['title'] }}</h2>
            <p>{{ $contactCta['description'] }}</p>
            <a class="btn btn-contact-cta" href="{{ $contactCta['button_url'] }}">{{ $contactCta['button_text'] }}</a>
        </div>
    </section>
    @endif
@endsection
