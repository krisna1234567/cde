@extends('layouts.admin')

@section('title', 'Projects')
@section('page_heading', 'Projects')

@section('content')
    <div class="admin-summary-grid mb-4">
        <article class="admin-summary-card">
            <span class="admin-summary-icon"><i class="bi bi-images"></i></span>
            <div><p>Total Projects</p><strong>{{ number_format($summary['total']) }}</strong></div>
        </article>
        <article class="admin-summary-card">
            <span class="admin-summary-icon"><i class="bi bi-check-circle"></i></span>
            <div><p>Active Projects</p><strong>{{ number_format($summary['active']) }}</strong></div>
        </article>
        <article class="admin-summary-card">
            <span class="admin-summary-icon"><i class="bi bi-star"></i></span>
            <div><p>Featured</p><strong>{{ number_format($summary['featured']) }}</strong></div>
        </article>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <h2>Project Items</h2>
                <p>Kelola project yang tampil pada halaman Projects publik.</p>
            </div>
            <a href="{{ route('admin.projects.create') }}" class="btn admin-primary-button"><i class="bi bi-plus-lg me-1"></i>Add Project</a>
        </div>

        <form method="GET" action="{{ route('admin.projects.index') }}" class="row g-2 mb-4">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" @selected($filters['status'] === 'active')>Active</option>
                    <option value="inactive" @selected($filters['status'] === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="featured" class="form-select">
                    <option value="">Featured + Regular</option>
                    <option value="1" @selected($filters['featured'] === '1')>Featured Only</option>
                    <option value="0" @selected($filters['featured'] === '0')>Regular Only</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected($filters['category'] === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="search" name="search" class="form-control" value="{{ $filters['search'] }}" placeholder="Search title, client, or location">
            </div>
            <div class="col-md-1 d-grid">
                <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Client</th>
                        <th>Category</th>
                        <th>Capacity</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($projects as $project)
                    <tr>
                        <td>
                            <strong>{{ $project->title }}</strong>
                            <small class="d-block text-muted">/{{ $project->slug }}</small>
                        </td>
                        <td>{{ $project->client_name ?: '-' }}</td>
                        <td>{{ $project->category ?: '-' }}</td>
                        <td>{{ $project->capacity ?: '-' }}</td>
                        <td>#{{ $project->sort_order }}</td>
                        <td>
                            <span class="badge {{ $project->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $project->is_active ? 'Active' : 'Inactive' }}</span>
                            @if ($project->is_featured)<span class="badge text-bg-warning ms-1">Featured</span>@endif
                        </td>
                        <td class="text-end">
                            <div class="admin-row-actions justify-content-end">
                                @if ($project->is_active)
                                    <a href="{{ route('projects.show', $project->slug) }}" target="_blank" rel="noopener" class="btn btn-sm btn-light" title="Preview"><i class="bi bi-box-arrow-up-right"></i></a>
                                @endif
                                <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square me-1"></i>Edit</a>
                                <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" data-confirm-delete="project ini">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="admin-empty-state">Belum ada project.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($projects->hasPages())
            <div class="mt-4">{{ $projects->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>
@endsection
