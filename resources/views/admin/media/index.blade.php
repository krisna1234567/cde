@extends('layouts.admin')

@section('title', 'Media')
@section('page_heading', 'Media')

@section('content')
    <div class="admin-summary-grid mb-4">
        <article class="admin-summary-card">
            <span class="admin-summary-icon"><i class="bi bi-newspaper"></i></span>
            <div><p>Total Posts</p><strong>{{ number_format($summary['total']) }}</strong></div>
        </article>
        <article class="admin-summary-card">
            <span class="admin-summary-icon"><i class="bi bi-check-circle"></i></span>
            <div><p>Published</p><strong>{{ number_format($summary['published']) }}</strong></div>
        </article>
        <article class="admin-summary-card">
            <span class="admin-summary-icon"><i class="bi bi-pencil"></i></span>
            <div><p>Drafts</p><strong>{{ number_format($summary['draft']) }}</strong></div>
        </article>
        <article class="admin-summary-card">
            <span class="admin-summary-icon"><i class="bi bi-star"></i></span>
            <div><p>Featured</p><strong>{{ number_format($summary['featured']) }}</strong></div>
        </article>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <h2>Media Posts</h2>
                <p>Kelola artikel dan berita yang tampil pada halaman Media publik.</p>
            </div>
            <a href="{{ route('admin.media.create') }}" class="btn admin-primary-button"><i class="bi bi-plus-lg me-1"></i>Add Post</a>
        </div>

        <form method="GET" action="{{ route('admin.media.index') }}" class="row g-2 mb-4">
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected($filters['status'] === $status->value)>{{ ucfirst($status->value) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
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
            <div class="col-md-5">
                <input type="search" name="search" class="form-control" value="{{ $filters['search'] }}" placeholder="Search title, slug, or category">
            </div>
            <div class="col-md-1 d-grid">
                <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Post</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Published</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($posts as $post)
                    <tr>
                        <td>
                            <strong>{{ $post->title }}</strong>
                            <small class="d-block text-muted">/{{ $post->slug }}</small>
                        </td>
                        <td>{{ $post->category ?: '-' }}</td>
                        <td>{{ $post->author?->name ?: 'System' }}</td>
                        <td>
                            @if ($post->published_at)
                                {{ $post->published_at->timezone(config('app.timezone'))->format('d M Y H:i') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $post->status === App\Enums\PublishStatus::Published ? 'text-bg-success' : 'text-bg-secondary' }}">{{ ucfirst($post->status->value) }}</span>
                            @if ($post->is_featured)<span class="badge text-bg-warning ms-1">Featured</span>@endif
                        </td>
                        <td class="text-end">
                            <div class="admin-row-actions justify-content-end">
                                @if ($post->status === App\Enums\PublishStatus::Published && $post->published_at && $post->published_at->lte(now()))
                                    <a href="{{ route('media.show', $post->slug) }}" target="_blank" rel="noopener" class="btn btn-sm btn-light" title="Preview"><i class="bi bi-box-arrow-up-right"></i></a>
                                @endif
                                <a href="{{ route('admin.media.edit', $post) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square me-1"></i>Edit</a>
                                <form method="POST" action="{{ route('admin.media.destroy', $post) }}" data-confirm-delete="post media ini">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="admin-empty-state">Belum ada post media.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($posts->hasPages())
            <div class="mt-4">{{ $posts->links() }}</div>
        @endif
    </div>
@endsection
