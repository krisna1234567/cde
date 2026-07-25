@extends('layouts.public')

@section('content')
    <x-public.page-hero :title="$hero['title']" :description="$hero['description']" :image="$hero['image']" :image-alt="$hero['image_alt']" />

    <section class="project-list-section">
        <div class="container-xl">
            <div class="inner-section-heading text-center">
                <h2>{{ $projectsSection['title'] }}</h2>
                @if ($projectsSection['subtitle'])<p>{{ $projectsSection['subtitle'] }}</p>@endif
            </div>
            <div class="projects-grid project-list-grid">
                @forelse ($projects as $project)
                    <x-public.project-card :project="$project" />
                @empty
                    <p class="text-center text-muted grid-column-full">No active projects are available yet.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
