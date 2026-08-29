@extends('layouts.admin')

@php($editing = $teamMember->exists)

@section('title', ($editing ? 'Edit' : 'Add').' Team Member')
@section('page_heading', ($editing ? 'Edit' : 'Add').' Team Member')

@section('content')
    <div class="admin-form-toolbar">
        <div>
            <h2>{{ $editing ? 'Update Team Member' : 'New Team Member' }}</h2>
            <p>Data ini langsung digunakan oleh section Meet Our Teams di halaman About.</p>
        </div>
        <a href="{{ route('admin.teams.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <form method="POST" enctype="multipart/form-data" class="admin-settings-form" action="{{ $editing ? route('admin.teams.update', $teamMember) : route('admin.teams.store') }}">
        @csrf
        @if($editing) @method('PUT') @endif

        <div class="admin-card mb-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Name *</label>
                    <input name="name" class="form-control" value="{{ old('name', $teamMember->name) }}" required maxlength="255">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sort Order *</label>
                    <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $teamMember->sort_order) }}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Position *</label>
                    <input name="position" class="form-control" value="{{ old('position', $teamMember->position) }}" required maxlength="255">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $teamMember->is_active))>
                        <label class="form-check-label">Active on frontend</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Description *</label>
                    <textarea name="description" maxlength="1000" class="form-control" rows="5" required>{{ old('description', $teamMember->description) }}</textarea>
                </div>
            </div>
        </div>

        <div class="admin-card mb-4">
            <div class="admin-card-header"><div><h2>Photo</h2><p>Gunakan foto portrait dengan komposisi wajah berada di tengah.</p></div></div>
            <div class="admin-upload-field">
                <div class="admin-upload-preview admin-upload-preview-og">
                    @if($media['photo'])
                        <img src="{{ $media['photo'] }}" alt="" data-preview-image="team-photo">
                    @else
                        <img src="" class="d-none" alt="" data-preview-image="team-photo"><i class="bi bi-image" data-preview-placeholder="team-photo"></i>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <input type="file" name="photo" accept="image/*" class="form-control" data-image-input data-preview-target="team-photo" @required(! $editing || blank($teamMember->photo_path))>
                    <div class="form-text">Format JPG, PNG, WEBP, atau SVG. Maksimal 4MB.</div>
                </div>
            </div>
        </div>

        <div class="admin-save-bar">
            <p class="mb-0">Urutan team member mengikuti Sort Order terkecil ke terbesar.</p>
            <button class="btn admin-primary-button"><i class="bi bi-check-lg me-1"></i>{{ $editing ? 'Update Team Member' : 'Create Team Member' }}</button>
        </div>
    </form>
@endsection
