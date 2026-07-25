@props(['title', 'description', 'image', 'imageAlt' => ''])
<section class="page-hero" style="background-image: linear-gradient(90deg, rgba(5, 28, 44, .64), rgba(5, 28, 44, .18)), url('{{ asset($image) }}');" aria-label="{{ $imageAlt }}">
    <div class="container-xl page-hero-content">
        <h1>{{ $title }}</h1>
        @if ($description)
            <p>{{ $description }}</p>
        @endif
    </div>
</section>
