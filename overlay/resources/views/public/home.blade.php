<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="min-vh-100 d-flex align-items-center bg-body-tertiary">
        <div class="container py-5 text-center">
            <span class="badge text-bg-primary mb-3">Laravel 10</span>
            <h1 class="display-5 fw-bold">Company Profile</h1>
            <p class="lead text-secondary mx-auto" style="max-width: 680px;">Fondasi aplikasi sudah siap. Konten public dinamis akan dibangun setelah migration dan modul CMS tersedia.</p>
            <a href="{{ route('login') }}" class="btn btn-dark px-4">Login Admin</a>
        </div>
    </main>
</body>
</html>
