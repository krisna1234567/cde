@props(['project'])
<article class="project-card">
    <img src="{{ asset($project['cover_image']) }}" alt="{{ $project['cover_image_alt'] }}" loading="lazy" width="900" height="560">
    <div class="project-overlay">
        <div>
            <h3>{{ $project['title'] }}</h3>
            <span>{{ $project['card_capacity'] }}</span>
        </div>
        <a class="project-detail-link" href="{{ route('projects.show', $project['slug']) }}" aria-label="View details for {{ $project['title'] }}">Detail</a>
    </div>
</article>
