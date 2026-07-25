<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js', 'resources/js/admin.js'])
</head>
<body class="admin-body">
    <div class="admin-shell">
        <aside class="admin-sidebar d-none d-lg-flex">
            @include('components.admin.sidebar')
        </aside>

        <div class="offcanvas offcanvas-start admin-mobile-sidebar" tabindex="-1" id="adminSidebar" aria-labelledby="adminSidebarLabel">
            <div class="offcanvas-header border-bottom">
                <h2 class="offcanvas-title fs-6" id="adminSidebarLabel">Admin Menu</h2>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-0">
                @include('components.admin.sidebar')
            </div>
        </div>

        <div class="admin-main">
            <header class="admin-topbar">
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn admin-menu-button d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar" aria-label="Open admin menu">
                        <i class="bi bi-list"></i>
                    </button>
                    <div>
                        <p class="admin-eyebrow mb-1">Content Management System</p>
                        <h1 class="admin-page-heading mb-0">@yield('page_heading', 'Dashboard')</h1>
                    </div>
                </div>

                <div class="dropdown">
                    <button class="btn admin-user-button dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="admin-user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li><span class="dropdown-item-text small text-muted">{{ auth()->user()->email }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </header>

            <main class="admin-content">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <div class="fw-semibold mb-1">Periksa kembali data yang diisi.</div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
