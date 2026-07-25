@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Dashboard</h1>
            <p class="text-secondary mb-0">Fondasi CMS Laravel 10 telah siap.</p>
        </div>
        <span class="badge text-bg-success align-self-start align-self-md-center px-3 py-2">Tahap 2 Selesai</span>
    </div>

    <div class="row g-3">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><span class="text-secondary small">Framework</span><h2 class="h5 mt-2 mb-0">Laravel 10</h2></div></div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><span class="text-secondary small">Authentication</span><h2 class="h5 mt-2 mb-0">Laravel Breeze</h2></div></div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><span class="text-secondary small">Frontend</span><h2 class="h5 mt-2 mb-0">Blade + Bootstrap 5</h2></div></div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><span class="text-secondary small">Asset Bundler</span><h2 class="h5 mt-2 mb-0">Vite</h2></div></div>
        </div>
    </div>

    <div class="alert alert-info mt-4 mb-0">
        Tahap berikutnya adalah membuat migration, model, relasi, seeder admin, dan middleware role Admin.
    </div>
@endsection
