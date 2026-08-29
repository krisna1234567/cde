@extends('layouts.public')

@section('content')
    <x-public.page-hero :title="$hero['title']" :description="$hero['description']" :image="$hero['image']" :image-alt="$hero['image_alt']" />

    <section class="about-history-section">
        <div class="container-xl">
            <h2>{{ $history['title'] }}</h2>
            @foreach ($history['paragraphs'] as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
        </div>
    </section>

    <section class="vision-mission-section">
        <div class="container-xl">
            <h2>{{ $visionMission['title'] }}</h2>
            <div class="vision-mission-grid">
                <article><h3>{{ $visionMission['vision_title'] }}</h3><p>{{ $visionMission['vision'] }}</p></article>
                <article><h3>{{ $visionMission['mission_title'] }}</h3><p>{{ $visionMission['mission'] }}</p></article>
            </div>
        </div>
    </section>

    @if ($advantagesSection['enabled'])
        <section class="why-cde-section">
            <div class="container-xl">
                <h2>{{ $advantagesSection['title'] }}</h2>
                <div class="why-cde-grid">
                    @foreach ($advantages as $advantage)
                        <article class="why-cde-item"><span>{{ $advantage['number'] }}</span><h3>{{ $advantage['title'] }}</h3><p>{{ $advantage['description'] }}</p></article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($teamSection['enabled'] && count($teams) > 0)
        <section id="meetTeams" class="team-section">
            <div class="container-xl">
                <h2>{{ $teamSection['title'] }}</h2>
                <div class="team-grid">
                    @foreach ($teams as $member)
                        <article class="team-card">
                            <img src="{{ asset($member['image']) }}" alt="{{ $member['image_alt'] }}" loading="lazy" width="520" height="380">
                            <div class="team-card-body">
                                <h3>{{ $member['name'] }}</h3>
                                <span>{{ $member['position'] }}</span>
                                <p>{{ $member['description'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
