<header class="site-header" data-site-header>
    <div class="container-xl">
        <nav class="navbar navbar-expand-lg public-navbar" aria-label="Main navigation">
            <a class="navbar-brand" href="{{ route('home') }}" aria-label="{{ $site['name'] }} home">
                <img src="{{ asset($site['logo']) }}" alt="{{ $site['logo_alt'] }}" width="161" height="46">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNavigation" aria-controls="publicNavigation" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-3"></i>
            </button>

            <div id="publicNavigation" class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                    @foreach ($navigation as $item)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs(...$item['patterns']) ? 'active' : '' }}" href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
                        </li>
                    @endforeach
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-contact-nav {{ request()->routeIs('contact.*') ? 'active' : '' }}" href="{{ route('contact.index') }}">{{ $site['contact_label'] }}</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
