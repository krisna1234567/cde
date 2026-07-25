@props(['post'])
<article class="media-card">
    <a class="media-card-image" href="{{ route('media.show', $post['slug']) }}">
        <img src="{{ asset($post['cover_image']) }}" alt="{{ $post['cover_image_alt'] }}" loading="lazy" width="900" height="560">
    </a>
    <div class="media-card-body">
        <p class="media-card-meta">{{ $post['published_date'] }} <span>•</span> {{ $post['published_time'] }}</p>
        <h2><a href="{{ route('media.show', $post['slug']) }}">{{ $post['title'] }}</a></h2>
        <p>{{ $post['excerpt'] }}</p>
    </div>
</article>
