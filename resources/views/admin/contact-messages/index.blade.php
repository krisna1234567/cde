@extends('layouts.admin')

@section('title', 'Contact Messages')
@section('page_heading', 'Contact Messages')

@section('content')
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h2>Visitor Messages</h2>
            <p>Menampilkan pesan yang dikirim pengunjung melalui form Contact publik.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.contact-messages.index') }}" class="row g-2 mb-4">
        <div class="col-md-10">
            <input type="search" name="search" class="form-control" value="{{ $filters['search'] }}" placeholder="Search name, email, phone, or message">
        </div>
        <div class="col-md-2 d-grid">
            <button class="btn btn-outline-secondary"><i class="bi bi-search me-1"></i>Filter</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table admin-table align-middle mb-0">
            <thead>
                <tr>
                    <th>Sender</th>
                    <th>Phone</th>
                    <th>Message</th>
                    <th>Received</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($messages as $message)
                <tr>
                    <td>
                        <strong>{{ $message->full_name ?: '-' }}</strong>
                        <small class="d-block text-muted">{{ $message->email }}</small>
                    </td>
                    <td>{{ $message->phone ?: '-' }}</td>
                    <td>
                        @if ($message->subject)
                        <strong class="d-block">{{ $message->subject }}</strong>
                        @endif
                        <span class="text-muted">{{ Illuminate\Support\Str::limit($message->message, 90) }}</span>
                    </td>
                    <td>
                        <time datetime="{{ $message->created_at?->toIso8601String() }}">{{ $message->created_at?->timezone(config('app.timezone'))->format('d M Y H:i') }}</time>
                        <small class="d-block text-muted">{{ $message->created_at?->diffForHumans() }}</small>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.contact-messages.show', $message) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i>View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="admin-empty-state">Belum ada pesan contact.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($messages->hasPages())
    <div class="mt-4">{{ $messages->links() }}</div>
    @endif
</div>
@endsection