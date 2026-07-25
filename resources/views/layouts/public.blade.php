<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-public.meta :meta="$meta" />
    @if ($site['favicon'])<link rel="icon" href="{{ asset($site['favicon']) }}">@endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="public-site {{ request()->routeIs('home') ? 'is-home-page' : 'is-inner-page' }}">
    <x-public.navbar :site="$site" :navigation="$navigation" />

    <main>
        @yield('content')
    </main>

    <x-public.footer :site="$site" :navigation="$navigation" :socials="$socials" />
    <x-public.whatsapp-button :site="$site" />
    @stack('scripts')
</body>
</html>
