@extends('layouts.public')

@section('content')
    <x-public.page-hero :title="$hero['title']" :description="$hero['description']" :image="$hero['image']" :image-alt="$hero['image_alt']" />

    @if($solutionsSection['enabled'])
        <section class="solutions-page-section">
            <div class="container-xl">
                <header class="solutions-heading">
                    @if($solutionsSection['title'])<h2>{{ $solutionsSection['title'] }}</h2>@endif
                    @if($solutionsSection['subtitle'])<p>{{ $solutionsSection['subtitle'] }}</p>@endif
                </header>
                <div class="solutions-grid">
                    @foreach($solutionsSection['items'] as $card)
                        @php
                            $link = $card['link_url'] ?? '';
                            $safeLink = preg_match('~^(https?://|/(?!/))~i', $link) ? url($link) : '#';
                        @endphp
                        <a class="solution-card" href="{{ $safeLink }}">
                            @if($card['image'])
                                <div class="solution-card-image">
                                    <img src="{{ asset($card['image']) }}" alt="{{ $card['image_alt'] }}" width="600" height="400">
                                </div>
                            @endif
                            @if($card['title'])<h3>{{ $card['title'] }}</h3>@endif
                            @if($card['description'])<p>{{ $card['description'] }}</p>@endif
                            @if($card['link_text'])<span class="solution-card-link">{{ $card['link_text'] }} <i class="bi bi-arrow-right" aria-hidden="true"></i></span>@endif
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
