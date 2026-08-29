@extends('layouts.admin')

@section('title', 'Testimonials')
@section('page_heading', 'Testimonials')

@section('content')
<div class="admin-summary-grid mb-4">
    <article class="admin-summary-card">
        <span class="admin-summary-icon"><i class="bi bi-chat-quote"></i></span>
        <div>
            <p>Total Testimonials</p><strong>{{ number_format($summary['total']) }}</strong>
        </div>
    </article>
    <article class="admin-summary-card">
        <span class="admin-summary-icon"><i class="bi bi-check-circle"></i></span>
        <div>
            <p>Active</p><strong>{{ number_format($summary['active']) }}</strong>
        </div>
    </article>
    <article class="admin-summary-card">
        <span class="admin-summary-icon"><i class="bi bi-eye-slash"></i></span>
        <div>
            <p>Inactive</p><strong>{{ number_format($summary['inactive']) }}</strong>
        </div>
    </article>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h2>Testimonial Items</h2>
            <p>Kelola testimonial yang tampil di section What Do They Say About CDE pada halaman Home.</p>
        </div>
        @if (! ($migrationMissing ?? false))
        <a href="{{ route('admin.testimonials.create') }}" class="btn admin-primary-button"><i class="bi bi-plus-lg me-1"></i>Add Testimonial</a>
        @endif
    </div>

    @if ($migrationMissing ?? false)
    <div class="alert alert-warning" role="alert">
        <div class="fw-semibold mb-1">Tabel testimonials belum tersedia.</div>
        Aktifkan write mode database lalu jalankan <code>php artisan migrate</code> agar fitur CRUD testimonial bisa digunakan.
    </div>
    @endif

    <form method="GET" action="{{ route('admin.testimonials.index') }}" class="row g-2 mb-4">
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="active" @selected($filters['status']==='active' )>Active</option>
                <option value="inactive" @selected($filters['status']==='inactive' )>Inactive</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="rating" class="form-select">
                <option value="">All Ratings</option>
                @for ($rating = 5; $rating >= 1; $rating--)
                <option value="{{ $rating }}" @selected((string) $filters['rating']===(string) $rating)>{{ $rating }} Stars</option>
                @endfor
            </select>
        </div>
        <div class="col-md-4">
            <input type="search" name="search" class="form-control" value="{{ $filters['search'] }}" placeholder="Search name, role, or quote">
        </div>
        <div class="col-md-2 d-grid">
            <button class="btn btn-outline-secondary"><i class="bi bi-search me-1"></i>Filter</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table admin-table align-middle mb-0">
            <thead>
                <tr>
                    <th>Person</th>
                    <th>Message</th>
                    <th>Rating</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($testimonials as $testimonial)
                <tr>
                    <td>
                        <strong>{{ $testimonial->name }}</strong>
                        <small class="d-block text-muted">{{ $testimonial->role ?: '-' }}</small>
                    </td>
                    <td><span class="text-muted">{{ Illuminate\Support\Str::limit($testimonial->quote, 95) }}</span></td>
                    <td>
                        <span class="text-warning" aria-label="{{ $testimonial->rating }} out of 5 stars">
                            @for ($i = 0; $i < $testimonial->rating; $i++)<i class="bi bi-star-fill"></i>@endfor
                        </span>
                    </td>
                    <td>#{{ $testimonial->sort_order }}</td>
                    <td><span class="badge {{ $testimonial->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $testimonial->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="text-end">
                        <div class="admin-row-actions justify-content-end">
                            @if ($testimonial->is_active)
                            <a href="{{ route('home') }}#testimonialViewport" target="_blank" rel="noopener" class="btn btn-sm btn-light" title="Preview"><i class="bi bi-box-arrow-up-right"></i></a>
                            @endif
                            <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square me-1"></i>Edit</a>
                            <form method="POST" action="{{ route('admin.testimonials.destroy', $testimonial) }}" data-confirm-delete="testimonial ini">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="admin-empty-state">Belum ada testimonial.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($testimonials->hasPages())
    <div class="mt-4">{{ $testimonials->links() }}</div>
    @endif
</div>
@endsection