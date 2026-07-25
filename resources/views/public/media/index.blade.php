@extends('layouts.public')

@section('content')
    <x-public.page-hero :title="$hero['title']" :description="$hero['description']" :image="$hero['image']" :image-alt="$hero['image_alt']" />

    <section class="media-list-section">
        <div class="container-xl">
            <div class="inner-section-heading text-center">
                <h2>{{ $newsSection['title'] }}</h2>
                @if ($newsSection['subtitle'])<p>{{ $newsSection['subtitle'] }}</p>@endif
            </div>
            <div class="media-grid">
                @forelse ($posts as $post)
                    <x-public.media-card :post="$post" />
                @empty
                    <p class="text-center text-muted grid-column-full">No published news is available yet.</p>
                @endforelse
            </div>
            {{ $posts->onEachSide(1)->links('components.public.pagination') }}
        </div>
    </section>
@endsection
