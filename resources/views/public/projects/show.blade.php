@extends('layouts.public')

@section('content')
    <section class="detail-page-section project-detail-page">
        <div class="container-xl">
            <div class="project-detail-header">
                <x-public.breadcrumb :items="[
                    ['label' => 'Project', 'url' => route('projects.index')],
                    ['label' => 'Project Details', 'url' => null],
                ]" />
                <h1>{{ $project['title'] }}</h1>
            </div>

            <div class="project-gallery">
                <figure class="project-gallery-main"><img src="{{ asset($project['main_image']) }}" alt="{{ $project['main_image_alt'] }}" width="900" height="560"></figure>
                <figure class="project-gallery-logo"><img src="{{ asset($project['client_logo']) }}" alt="{{ $project['client_logo_alt'] }}" loading="lazy" width="520" height="240"></figure>
                <figure class="project-gallery-secondary"><img src="{{ asset($project['secondary_image']) }}" alt="{{ $project['secondary_image_alt'] }}" loading="lazy" width="1488" height="453"></figure>
            </div>

            <article class="project-description">
                <h2>About {{ $project['title'] }}</h2>
                <p>{{ $project['description'] }}</p>
                <dl class="project-specification">
                    <div><dt>Specification</dt><dd>{{ $project['capacity'] ?: '-' }}</dd></div>
                    <div><dt>Location</dt><dd>{{ $project['location'] ?: '-' }}</dd></div>
                    <div><dt>Overview</dt><dd>{{ $project['overview'] ?: '-' }}</dd></div>
                </dl>
            </article>
        </div>
    </section>
@endsection
