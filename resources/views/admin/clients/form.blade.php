@extends('layouts.admin')

@php($editing = $client->exists)

@section('title', ($editing ? 'Edit' : 'Add').' Client')
@section('page_heading', ($editing ? 'Edit' : 'Add').' Client')

@section('content')
    <div class="admin-form-toolbar">
        <div>
            <h2>{{ $editing ? 'Update Client Logo' : 'New Client Logo' }}</h2>
            <p>Data ini langsung digunakan oleh slider Trusted Companies di halaman Home.</p>
        </div>
        <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <form method="POST" enctype="multipart/form-data" class="admin-settings-form" action="{{ $editing ? route('admin.clients.update', $client) : route('admin.clients.store') }}">
        @csrf
        @if($editing) @method('PUT') @endif

        <div class="admin-card mb-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Client Name *</label>
                    <input name="name" class="form-control" value="{{ old('name', $client->name) }}" required maxlength="255">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sort Order *</label>
                    <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $client->sort_order) }}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Website URL</label>
                    <input type="url" name="website_url" class="form-control" value="{{ old('website_url', $client->website_url) }}" placeholder="https://">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $client->is_active))>
                        <label class="form-check-label">Active on frontend</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-card mb-4">
            <div class="admin-card-header"><div><h2>Client Logo</h2><p>Gunakan logo transparan agar tampil rapi di lingkaran putih slider.</p></div></div>
            <div class="admin-upload-field">
                <div class="admin-upload-preview admin-upload-preview-og">
                    @if($media['logo'])
                        <img src="{{ $media['logo'] }}" alt="" data-preview-image="client-logo">
                    @else
                        <img src="" class="d-none" alt="" data-preview-image="client-logo"><i class="bi bi-image" data-preview-placeholder="client-logo"></i>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <input type="file" name="logo" accept="image/*" class="form-control" data-image-input data-preview-target="client-logo" data-remove-target="remove_logo" @required(! $editing)>
                    <input name="logo_alt" class="form-control mt-2" value="{{ old('logo_alt', $client->logo_alt) }}" placeholder="Logo alternative text">
                    @if ($editing)
                        <div class="form-check mt-2">
                            <input id="remove_logo" type="checkbox" class="form-check-input" name="remove_logo" value="1">
                            <label for="remove_logo" class="form-check-label">Remove current logo</label>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="admin-save-bar">
            <p class="mb-0">Logo aktif akan tampil sesuai Sort Order terkecil ke terbesar.</p>
            <button class="btn admin-primary-button"><i class="bi bi-check-lg me-1"></i>{{ $editing ? 'Update Client' : 'Create Client' }}</button>
        </div>
    </form>
@endsection
