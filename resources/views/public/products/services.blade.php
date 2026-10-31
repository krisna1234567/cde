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
                        <span class="service-proposal-label"><i class="bi bi-file-earmark-pdf" aria-hidden="true"></i> Lihat &amp; Download Proposal</span>
                        <button type="button" class="service-proposal-trigger" data-bs-toggle="modal" data-bs-target="#serviceProposalModal" data-proposal-url="{{ $service['proposal_url'] }}" data-service-title="{{ $service['title'] }}" aria-label="View proposal for {{ $service['title'] }}"></button>
                    </article>
                @empty
                    <p class="text-center text-muted grid-column-full">Belum ada layanan yang aktif.</p>
                @endforelse
            </div>
        </div>
    </section>

    @include('public.partials.service-proposal-modal')
@endsection
