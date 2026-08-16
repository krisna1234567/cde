@extends('layouts.admin')

@section('title', 'Pages & Sections')
@section('page_heading', 'Pages & Sections')

@section('content')
<div class="admin-card">
    <div class="admin-card-header">
        <div><h2>Website Pages</h2><p>Edit informasi halaman, SEO, section, dan item konten.</p></div>
    </div>
    <div class="table-responsive">
        <table class="table admin-table align-middle mb-0">
            <thead><tr><th>Page</th><th>Slug</th><th>Sections</th><th>Navigation</th><th>Status</th><th class="text-end">Action</th></tr></thead>
            <tbody>
            @forelse($pages as $page)
                <tr>
                    <td><strong>{{ $page->name }}</strong><small class="d-block text-muted">{{ $page->page_key }}</small></td>
                    <td><code>/{{ $page->slug === 'home' ? '' : $page->slug }}</code></td>
                    <td>{{ $page->active_sections_count }}/{{ $page->sections_count }} active</td>
                    <td>{{ $page->show_in_navigation ? 'Visible · #'.$page->navigation_order : 'Hidden' }}</td>
                    <td><span class="badge {{ $page->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $page->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="text-end"><a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square me-1"></i>Edit</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="admin-empty-state">Belum ada halaman.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
