@extends('layouts.admin')

@section('title', 'Contact Message Detail')
@section('page_heading', 'Contact Message')

@section('content')
<div class="admin-form-toolbar">
    <div>
        <h2>{{ $contactMessage->full_name ?: 'Visitor Message' }}</h2>
        <p>Detail pesan dari form Contact publik.</p>
    </div>
    <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-outline-secondary">Back</a>
</div>

<div class="admin-card mb-4">
    <div class="admin-card-header">
        <div>
            <h2>Sender Information</h2>
            <p>Data ini tersimpan otomatis saat pengunjung mengirim form Contact.</p>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="admin-content-block h-100">
                <small class="d-block text-muted mb-1">Name</small>
                <strong>{{ $contactMessage->full_name ?: '-' }}</strong>
            </div>
        </div>
        <div class="col-md-6">
            <div class="admin-content-block h-100">
                <small class="d-block text-muted mb-1">Email</small>
                <span class="text-break">{{ $contactMessage->email }}</span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="admin-content-block h-100">
                <small class="d-block text-muted mb-1">Phone</small>
                <span>{{ $contactMessage->phone ?: '-' }}</span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="admin-content-block h-100">
                <small class="d-block text-muted mb-1">Received</small>
                <time datetime="{{ $contactMessage->created_at?->toIso8601String() }}">{{ $contactMessage->created_at?->timezone(config('app.timezone'))->format('d M Y H:i') }}</time>
            </div>
        </div>
        <div class="col-12">
            <div class="admin-content-block">
                <small class="d-block text-muted mb-1">Source URL</small>
                <span class="text-break">{{ $contactMessage->source_url ?: '-' }}</span>
            </div>
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h2>Message</h2>
            <!-- <p>Isi pesan ditampilkan apa adanya tanpa perubahan dari admin.</p> -->
        </div>
    </div>

    @if ($contactMessage->subject)
    <div class="admin-content-block mb-3">
        <small class="d-block text-muted mb-1">Subject</small>
        <strong>{{ $contactMessage->subject }}</strong>
    </div>
    @endif

    <div class="admin-content-block">
        <p class="mb-0 text-break">{!! nl2br(e($contactMessage->message)) !!}</p>
    </div>
</div>
@endsection