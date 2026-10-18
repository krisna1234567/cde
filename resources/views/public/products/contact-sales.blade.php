@extends('layouts.public')

@section('content')
    <section class="sales-page-section">
        <div class="container-xl">
            <x-public.breadcrumb :items="[
                ['label' => 'Product & Service', 'url' => route('products.index')],
                ['label' => 'Product Details', 'url' => route('products.show', $product->slug)],
                ['label' => 'Contact Sales', 'url' => null],
            ]" />
            <div class="sales-form-wrap">
                <header class="sales-form-heading">
                    <img src="{{ asset($site['logo']) }}" alt="{{ $site['name'] }}" width="150" height="65">
                    <h1>Let's Find the Right Solution for You</h1>
                    <p>Connect with our sales team to learn more about our solar products, discuss your project requirements, and get a tailored quotation.</p>
                </header>
                @if(session('success'))
                    <div class="alert alert-success" role="status">{{ session('success') }}</div>
                @endif
                @include('public.partials.contact-form', [
                    'formAction' => route('products.sales.store', $product->slug),
                    'defaultMessage' => $defaultMessage,
                ])
            </div>
        </div>
    </section>
@endsection
