@extends('layouts.admin')

@section('title', 'Products & Services')
@section('page_heading', 'Products & Services')

@section('content')
    <div class="admin-summary-grid mb-4">
        <article class="admin-summary-card">
            <span class="admin-summary-icon"><i class="bi bi-box-seam"></i></span>
            <div><p>Products</p><strong>{{ number_format($summary['products']) }}</strong></div>
        </article>
        <article class="admin-summary-card">
            <span class="admin-summary-icon"><i class="bi bi-tools"></i></span>
            <div><p>Services</p><strong>{{ number_format($summary['services']) }}</strong></div>
        </article>
        <article class="admin-summary-card">
            <span class="admin-summary-icon"><i class="bi bi-check-circle"></i></span>
            <div><p>Active Items</p><strong>{{ number_format($summary['active']) }}</strong></div>
        </article>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <h2>Product & Service Items</h2>
                <p>Kelola item yang tampil pada halaman Product & Service publik.</p>
            </div>
            <a href="{{ route('admin.services.create') }}" class="btn admin-primary-button"><i class="bi bi-plus-lg me-1"></i>Add Item</a>
        </div>

        <form method="GET" action="{{ route('admin.services.index') }}" class="row g-2 mb-4">
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->value }}" @selected($filters['type'] === $type->value)>{{ ucfirst($type->value) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" @selected($filters['status'] === 'active')>Active</option>
                    <option value="inactive" @selected($filters['status'] === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="search" name="search" class="form-control" value="{{ $filters['search'] }}" placeholder="Search name, brand, or slug">
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-outline-secondary"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Type</th>
                        <th>Brand / Icon</th>
                        <th>Price</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($services as $service)
                    <tr>
                        <td>
                            <strong>{{ $service->name }}</strong>
                            <small class="d-block text-muted">/{{ $service->slug }}</small>
                        </td>
                        <td><span class="badge text-bg-light">{{ ucfirst($service->item_type->value) }}</span></td>
                        <td>
                            @if ($service->item_type === App\Enums\ServiceType::Product)
                                {{ $service->brand ?: '-' }}
                            @else
                                <i class="bi {{ $service->icon ?: 'bi-check-circle' }} me-1"></i>{{ $service->icon ?: 'bi-check-circle' }}
                            @endif
                        </td>
                        <td>
                            @if ($service->price !== null)
                                {{ $service->currency }} {{ number_format((float) $service->price, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td>#{{ $service->sort_order }}</td>
                        <td>
                            <span class="badge {{ $service->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $service->is_active ? 'Active' : 'Inactive' }}</span>
                            @if ($service->is_featured)<span class="badge text-bg-warning ms-1">Featured</span>@endif
                        </td>
                        <td class="text-end">
                            <div class="admin-row-actions justify-content-end">
                                @if ($service->item_type === App\Enums\ServiceType::Product && $service->is_active)
                                    <a href="{{ route('products.show', $service->slug) }}" target="_blank" rel="noopener" class="btn btn-sm btn-light" title="Preview"><i class="bi bi-box-arrow-up-right"></i></a>
                                @endif
                                <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square me-1"></i>Edit</a>
                                <form method="POST" action="{{ route('admin.services.destroy', $service) }}" data-confirm-delete="item ini">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="admin-empty-state">Belum ada product atau service.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($services->hasPages())
            <div class="mt-4">{{ $services->links() }}</div>
        @endif
    </div>
@endsection
