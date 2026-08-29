@extends('layouts.admin')

@php
    $editing = $post->exists;
    $publishedAt = old('published_at', optional($post->published_at)->format('Y-m-d\TH:i'));
@endphp

@section('title', ($editing ? 'Edit' : 'Add').' Media')
@section('page_heading', ($editing ? 'Edit' : 'Add').' Media')

@section('content')
    <div class="admin-form-toolbar">
        <div>
            <h2>{{ $editing ? 'Update Post' : 'New Post' }}</h2>
            <p>Data ini langsung digunakan oleh halaman Media publik.</p>
        </div>
        <a href="{{ route('admin.media.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <form method="POST" enctype="multipart/form-data" class="admin-settings-form" action="{{ $editing ? route('admin.media.update', $post) : route('admin.media.store') }}">
        @csrf
        @if($editing) @method('PUT') @endif

        <div class="admin-card mb-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Title *</label>
                    <input name="title" class="form-control" value="{{ old('title', $post->title) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <input name="category" class="form-control" value="{{ old('category', $post->category) }}" placeholder="News">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Slug</label>
                    <input name="slug" class="form-control" value="{{ old('slug', $post->slug) }}" placeholder="auto-generated-from-title">
                    <div class="form-text">Kosongkan untuk membuat slug otomatis dari title.</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-select" required>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $post->status?->value ?? 'draft') === $status->value)>{{ ucfirst($status->value) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Published At</label>
                    <input type="datetime-local" name="published_at" class="form-control" value="{{ $publishedAt }}">
                    <div class="form-text">Agar tampil di frontend, status harus Published dan waktu ini tidak boleh di masa depan.</div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input type="hidden" name="is_featured" value="0">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $post->is_featured))>
                        <label class="form-check-label">Featured</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Excerpt</label>
                    <textarea name="excerpt" maxlength="1000" class="form-control" rows="3">{{ old('excerpt', $post->excerpt) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Content *</label>
                    <textarea name="content" class="form-control" rows="10" data-wysiwyg>{{ old('content', $post->content) }}</textarea>
                    <div class="form-text">Gunakan editor untuk format paragraf, bold, italic, underline, dan link.</div>
                </div>
            </div>
        </div>

        <div class="admin-card mb-4">
            <div class="admin-card-header"><div><h2>Cover Image</h2><p>Dipakai di kartu Media, detail artikel, dan fallback OG image.</p></div></div>
            <div class="admin-upload-field">
                <div class="admin-upload-preview admin-upload-preview-og">
                    @if($media['cover_image'])
                        <img src="{{ $media['cover_image'] }}" alt="" data-preview-image="media-cover">
                    @else
                        <img src="" class="d-none" alt="" data-preview-image="media-cover"><i class="bi bi-image" data-preview-placeholder="media-cover"></i>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <input type="file" name="cover_image" accept="image/*" class="form-control" data-image-input data-preview-target="media-cover" data-remove-target="remove_cover_image">
                    <input name="cover_image_alt" class="form-control mt-2" value="{{ old('cover_image_alt', $post->cover_image_alt) }}" placeholder="Cover image alternative text">
                    <div class="form-check mt-2">
                        <input id="remove_cover_image" type="checkbox" class="form-check-input" name="remove_cover_image" value="1">
                        <label for="remove_cover_image" class="form-check-label">Remove current image</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-save-bar">
            <p class="mb-0">SEO dibuat otomatis dari title, excerpt, content, slug, dan cover image.</p>
            <button class="btn admin-primary-button"><i class="bi bi-check-lg me-1"></i>{{ $editing ? 'Update Post' : 'Create Post' }}</button>
        </div>
    </form>
@endsection
