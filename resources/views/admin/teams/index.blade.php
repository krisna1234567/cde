@extends('layouts.admin')

@section('title', 'Teams')
@section('page_heading', 'Teams')

@section('content')
    <div class="admin-summary-grid mb-4">
        <article class="admin-summary-card">
            <span class="admin-summary-icon"><i class="bi bi-people"></i></span>
            <div><p>Total Team</p><strong>{{ number_format($summary['total']) }}</strong></div>
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
                <h2>Team Members</h2>
                <p>Kelola data yang tampil pada section Meet Our Teams di halaman About.</p>
            </div>
            <a href="{{ route('admin.teams.create') }}" class="btn admin-primary-button"><i class="bi bi-plus-lg me-1"></i>Add Team</a>
        </div>

        <form method="GET" action="{{ route('admin.teams.index') }}" class="row g-2 mb-4">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" @selected($filters['status'] === 'active')>Active</option>
                    <option value="inactive" @selected($filters['status'] === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-md-7">
                <input type="search" name="search" class="form-control" value="{{ $filters['search'] }}" placeholder="Search name, position, or description">
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-outline-secondary"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Photo</th>
                        <th>Description</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($teamMembers as $member)
                    <tr>
                        <td>
                            <strong>{{ $member->name }}</strong>
                            @if ($member->is_ceo)<span class="badge text-bg-primary ms-1">CEO</span>@endif
                            <small class="d-block text-muted">{{ $member->position ?: '-' }}</small>
                        </td>
                        <td>
                            @if ($member->photoUrl())
                                <img src="{{ $member->photoUrl() }}" alt="" style="width: 58px; height: 58px; border-radius: 50%; object-fit: cover;">
                            @else
                                <span class="text-muted">No photo</span>
                            @endif
                        </td>
                        <td><span class="text-muted">{{ Illuminate\Support\Str::limit($member->description, 95) }}</span></td>
                        <td>#{{ $member->sort_order }}</td>
                        <td><span class="badge {{ $member->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $member->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="text-end">
                            <div class="admin-row-actions justify-content-end">
                                @if ($member->is_active)
                                    <a href="{{ route('about') }}#meetTeams" target="_blank" rel="noopener" class="btn btn-sm btn-light" title="Preview"><i class="bi bi-box-arrow-up-right"></i></a>
                                @endif
                                <a href="{{ route('admin.teams.edit', $member) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square me-1"></i>Edit</a>
                                <form method="POST" action="{{ route('admin.teams.destroy', $member) }}" data-confirm-delete="team member ini">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="admin-empty-state">Belum ada team member.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($teamMembers->hasPages())
            <div class="mt-4">{{ $teamMembers->links() }}</div>
        @endif
    </div>
@endsection
