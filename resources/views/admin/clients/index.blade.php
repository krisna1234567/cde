@extends('layouts.admin')

@section('title', 'Clients')
@section('page_heading', 'Clients')

@section('content')
    <div class="admin-summary-grid mb-4">
        <article class="admin-summary-card">
            <span class="admin-summary-icon"><i class="bi bi-building-check"></i></span>
            <div><p>Total Clients</p><strong>{{ number_format($summary['total']) }}</strong></div>
        </article>
        <article class="admin-summary-card">
            <span class="admin-summary-icon"><i class="bi bi-check-circle"></i></span>
            <div><p>Active</p><strong>{{ number_format($summary['active']) }}</strong></div>
        </article>
        <article class="admin-summary-card">
            <span class="admin-summary-icon"><i class="bi bi-eye-slash"></i></span>
            <div><p>Inactive</p><strong>{{ number_format($summary['inactive']) }}</strong></div>
        </article>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <h2>Client Logos</h2>
                <p>Kelola logo yang tampil pada slider Trusted Companies di halaman Home.</p>
            </div>
            <a href="{{ route('admin.clients.create') }}" class="btn admin-primary-button"><i class="bi bi-plus-lg me-1"></i>Add Client</a>
        </div>

        <form method="GET" action="{{ route('admin.clients.index') }}" class="row g-2 mb-4">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" @selected($filters['status'] === 'active')>Active</option>
                    <option value="inactive" @selected($filters['status'] === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-md-7">
                <input type="search" name="search" class="form-control" value="{{ $filters['search'] }}" placeholder="Search client name or website">
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-outline-secondary"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Logo</th>
                        <th>Website</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($clients as $client)
                    <tr>
                        <td>
                            <strong>{{ $client->name }}</strong>
                            <small class="d-block text-muted">{{ $client->logo_alt ?: $client->name.' logo' }}</small>
                        </td>
                        <td>
                            @if ($client->logoUrl())
                                <img src="{{ $client->logoUrl() }}" alt="" style="width: 86px; height: 46px; object-fit: contain;">
                            @else
                                <span class="text-muted">No logo</span>
                            @endif
                        </td>
                        <td>
                            @if ($client->website_url)
                                <a href="{{ $client->website_url }}" target="_blank" rel="noopener">{{ parse_url($client->website_url, PHP_URL_HOST) ?: $client->website_url }}</a>
                            @else
                                -
                            @endif
                        </td>
                        <td>#{{ $client->sort_order }}</td>
                        <td><span class="badge {{ $client->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $client->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="text-end">
                            <div class="admin-row-actions justify-content-end">
                                @if ($client->is_active)
                                    <a href="{{ route('home') }}#trustedClients" target="_blank" rel="noopener" class="btn btn-sm btn-light" title="Preview"><i class="bi bi-box-arrow-up-right"></i></a>
                                @endif
                                <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square me-1"></i>Edit</a>
                                <form method="POST" action="{{ route('admin.clients.destroy', $client) }}" data-confirm-delete="client logo ini">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="admin-empty-state">Belum ada client logo.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($clients->hasPages())
            <div class="mt-4">{{ $clients->links() }}</div>
        @endif
    </div>
@endsection
