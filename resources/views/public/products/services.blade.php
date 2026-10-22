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
                        <span class="service-proposal-label">View Proposal <i class="bi bi-file-earmark-pdf" aria-hidden="true"></i></span>
                        <button type="button" class="service-proposal-trigger" data-bs-toggle="modal" data-bs-target="#serviceProposalModal" data-proposal-url="{{ $service['proposal_url'] }}" data-service-title="{{ $service['title'] }}" aria-label="View proposal for {{ $service['title'] }}"></button>
                    </article>
                @empty
                    <p class="text-center text-muted grid-column-full">No active services are available yet.</p>
                @endforelse
            </div>
        </div>
    </section>
    <div class="modal fade" id="serviceProposalModal" tabindex="-1" aria-labelledby="serviceProposalTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="serviceProposalTitle">Service Proposal</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close proposal"></button>
                </div>
                <div class="modal-body service-proposal-body">
                    <p data-proposal-empty role="status" class="text-center text-muted p-4">The proposal for this service is not available yet. Please contact our team for more information.</p>
                    <div data-proposal-viewer></div>
                </div>
                <div class="modal-footer">
                    <a data-proposal-open class="btn btn-outline-secondary" href="#" target="_blank" rel="noopener" hidden>Open PDF in New Tab <i class="bi bi-box-arrow-up-right ms-1" aria-hidden="true"></i></a>
                    <a class="btn contact-submit" href="{{ route('contact.index') }}">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
@endsection
