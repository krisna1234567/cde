@extends('layouts.admin')

@php($editing = $project->exists)

@section('title', ($editing ? 'Edit' : 'Add').' Project')
@section('page_heading', ($editing ? 'Edit' : 'Add').' Project')

@section('content')
    <div class="admin-form-toolbar">
        <div>
            <h2>{{ $editing ? 'Update Project' : 'New Project' }}</h2>
            <p>Data ini langsung digunakan oleh halaman Projects publik.</p>
        </div>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <form method="POST" enctype="multipart/form-data" class="admin-settings-form" action="{{ $editing ? route('admin.projects.update', $project) : route('admin.projects.store') }}">
        @csrf
        @if($editing) @method('PUT') @endif

        <div class="admin-card mb-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Project Title *</label>
                    <input name="title" class="form-control" value="{{ old('title', $project->title) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Client Name</label>
                    <input name="client_name" class="form-control" value="{{ old('client_name', $project->client_name) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Slug</label>
                    <input name="slug" class="form-control" value="{{ old('slug', $project->slug) }}" placeholder="auto-generated-from-title">
                    <div class="form-text">Kosongkan untuk membuat slug otomatis dari title.</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <input name="category" class="form-control" value="{{ old('category', $project->category) }}" placeholder="Solar PV">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Project Date</label>
                    <input type="date" name="project_date" class="form-control" value="{{ old('project_date', optional($project->project_date)->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Capacity</label>
                    <input name="capacity" maxlength="100" class="form-control" value="{{ old('capacity', $project->capacity) }}" placeholder="405 kWp">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sort Order *</label>
                    <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $project->sort_order) }}" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input type="hidden" name="is_featured" value="0">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $project->is_featured))>
                        <label class="form-check-label">Featured</label>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $project->is_active))>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Location</label>
                    <textarea name="location" maxlength="1000" class="form-control" rows="2">{{ old('location', $project->location) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Short Description</label>
                    <textarea name="short_description" maxlength="1000" class="form-control" rows="3">{{ old('short_description', $project->short_description) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="5">{{ old('description', $project->description) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Overview</label>
                    <textarea name="overview" class="form-control" rows="5">{{ old('overview', $project->overview) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Project URL</label>
                    <input type="url" name="project_url" class="form-control" value="{{ old('project_url', $project->project_url) }}">
                </div>
            </div>
        </div>

        <div class="admin-card mb-4">
            <div class="admin-card-header"><div><h2>Project Media</h2><p>Gambar ini dipakai oleh grid dan detail Projects publik.</p></div></div>
            <div class="row g-3">
                <div class="col-lg-6">
                    @include('admin.projects.partials.image-field', [
                        'label' => 'Cover Image',
                        'input' => 'cover_image',
                        'removeInput' => 'remove_cover_image',
                        'altInput' => 'cover_image_alt',
                        'altValue' => old('cover_image_alt', $project->cover_image_alt),
                        'previewKey' => 'project-cover',
                        'image' => $media['cover_image'],
                    ])
                </div>
                <div class="col-lg-6">
                    @include('admin.projects.partials.image-field', [
                        'label' => 'Main Detail Image',
                        'input' => 'main_image',
                        'removeInput' => 'remove_main_image',
                        'altInput' => 'main_image_alt',
                        'altValue' => old('main_image_alt', $project->main_image_alt),
                        'previewKey' => 'project-main',
                        'image' => $media['main_image'],
                    ])
                </div>
                <div class="col-lg-6">
                    @include('admin.projects.partials.image-field', [
                        'label' => 'Secondary Detail Image',
                        'input' => 'secondary_image',
                        'removeInput' => 'remove_secondary_image',
                        'altInput' => 'secondary_image_alt',
                        'altValue' => old('secondary_image_alt', $project->secondary_image_alt),
                        'previewKey' => 'project-secondary',
                        'image' => $media['secondary_image'],
                    ])
                </div>
                <div class="col-lg-6">
                    @include('admin.projects.partials.image-field', [
                        'label' => 'Client Logo',
                        'input' => 'client_logo',
                        'removeInput' => 'remove_client_logo',
                        'altInput' => 'client_logo_alt',
                        'altValue' => old('client_logo_alt', $project->client_logo_alt),
                        'previewKey' => 'project-client-logo',
                        'image' => $media['client_logo'],
                    ])
                </div>
            </div>
        </div>

        <div class="admin-save-bar">
            <p class="mb-0">SEO dibuat otomatis dari title, deskripsi, slug, dan gambar project.</p>
            <button class="btn admin-primary-button"><i class="bi bi-check-lg me-1"></i>{{ $editing ? 'Update Project' : 'Create Project' }}</button>
        </div>
    </form>
@endsection
