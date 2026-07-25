@extends('layouts.public')

@section('content')
    <section class="detail-page-section media-detail-page">
        <div class="container-xl">
            <x-public.breadcrumb :items="[
                ['label' => 'Media', 'url' => route('media.index')],
                ['label' => $post['category'] ?: 'News', 'url' => null],
            ]" />

            <article class="media-detail-article">
                <header>
                    <h1>{{ $post['title'] }}</h1>
                    <p>{{ $post['author'] }} <span>•</span> {{ $post['published_date'] }} <span>•</span> {{ $post['published_time'] }}</p>
                </header>
                <figure><img src="{{ asset($post['cover_image']) }}" alt="{{ $post['cover_image_alt'] }}" width="1450" height="650"></figure>
                <div class="media-article-content">
                    @foreach ($post['content'] as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
            </article>
        </div>
    </section>
@endsection
