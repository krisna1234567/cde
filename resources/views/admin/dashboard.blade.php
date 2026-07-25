@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_heading', 'Dashboard')

@section('content')
    @php
        $cards = [
            ['label' => 'Products', 'value' => $summary['products'], 'icon' => 'bi-box-seam'],
            ['label' => 'Services', 'value' => $summary['services'], 'icon' => 'bi-tools'],
            ['label' => 'Projects', 'value' => $summary['portfolios'], 'icon' => 'bi-images'],
            ['label' => 'Media Posts', 'value' => $summary['posts'], 'icon' => 'bi-newspaper'],
            ['label' => 'New Messages', 'value' => $summary['new_messages'], 'icon' => 'bi-envelope-exclamation'],
        ];
    @endphp

    <div class="admin-summary-grid mb-4">
        @foreach ($cards as $card)
            <article class="admin-summary-card">
                <span class="admin-summary-icon"><i class="bi {{ $card['icon'] }}"></i></span>
                <div>
                    <p>{{ $card['label'] }}</p>
                    <strong>{{ number_format($card['value']) }}</strong>
                </div>
            </article>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <section class="admin-card h-100">
                <div class="admin-card-header">
                    <div>
                        <h2>Recent Activity</h2>
                        <p>Perubahan konten terbaru oleh administrator.</p>
                    </div>
                </div>

                @forelse ($activities as $activity)
                    <div class="admin-activity-item">
                        <span class="admin-activity-dot"></span>
                        <div class="flex-grow-1">
                            <div class="d-flex flex-wrap justify-content-between gap-2">
                                <strong>{{ $activity->description }}</strong>
                                <time class="text-muted small" datetime="{{ $activity->created_at?->toIso8601String() }}">{{ $activity->created_at?->diffForHumans() }}</time>
                            </div>
                            <p class="mb-0">{{ $activity->user?->name ?? 'System' }} · {{ ucfirst($activity->event) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="admin-empty-state">
                        <i class="bi bi-clock-history"></i>
                        <p>Belum ada aktivitas admin.</p>
                    </div>
                @endforelse
            </section>
        </div>

        <div class="col-xl-4">
            <section class="admin-card h-100">
                <div class="admin-card-header">
                    <div>
                        <h2>Quick Actions</h2>
                        <p>Akses pengaturan utama website.</p>
                    </div>
                </div>
                <a href="{{ route('admin.settings.edit') }}" class="admin-quick-link">
                    <span><i class="bi bi-sliders"></i></span>
                    <div><strong>Site Settings</strong><small>Brand, kontak, Maps, sosial media, dan SEO.</small></div>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="{{ route('home') }}" target="_blank" rel="noopener" class="admin-quick-link">
                    <span><i class="bi bi-globe2"></i></span>
                    <div><strong>Preview Website</strong><small>Buka halaman public di tab baru.</small></div>
                    <i class="bi bi-box-arrow-up-right"></i>
                </a>
            </section>
        </div>
    </div>
@endsection
