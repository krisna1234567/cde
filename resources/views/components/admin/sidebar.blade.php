<div class="admin-sidebar-inner">
    <a href="{{ route('admin.dashboard') }}" class="admin-brand text-decoration-none">
        <span class="admin-brand-mark"><i class="bi bi-sun-fill"></i></span>
        <span>
            <strong>{{ config('app.name') }}</strong>
            <small>Administrator</small>
        </span>
    </a>

    <nav class="admin-nav" aria-label="Admin navigation">
        <p class="admin-nav-label">Overview</p>
        <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
        </a>

        <p class="admin-nav-label mt-4">Website</p>
        <a href="{{ route('admin.settings.edit') }}" class="admin-nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="bi bi-sliders"></i><span>Site Settings</span>
        </a>
        <span class="admin-nav-link disabled"><i class="bi bi-layout-text-window-reverse"></i><span>Pages & Sections</span><small>Soon</small></span>
        <span class="admin-nav-link disabled"><i class="bi bi-box-seam"></i><span>Products & Services</span><small>Soon</small></span>
        <span class="admin-nav-link disabled"><i class="bi bi-images"></i><span>Projects</span><small>Soon</small></span>
        <span class="admin-nav-link disabled"><i class="bi bi-newspaper"></i><span>Media</span><small>Soon</small></span>
        <span class="admin-nav-link disabled"><i class="bi bi-envelope"></i><span>Contact Messages</span><small>Soon</small></span>
    </nav>

    <div class="admin-sidebar-footer">
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="admin-preview-link"><i class="bi bi-box-arrow-up-right"></i>View Website</a>
    </div>
</div>
