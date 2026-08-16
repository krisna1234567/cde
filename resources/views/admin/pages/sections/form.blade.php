@extends('layouts.admin')
@php($editing = $section->exists)
@section('title', ($editing ? 'Edit' : 'Add').' Section')
@section('page_heading', ($editing ? 'Edit' : 'Add').' Section — '.$page->name)

@section('content')
<div class="admin-form-toolbar"><div><h2>{{ $editing ? 'Update Section' : 'New Section' }}</h2><p>Section key menjadi identifier yang dibaca oleh template frontend.</p></div><a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-outline-secondary">Back</a></div>
<form method="POST" enctype="multipart/form-data" class="admin-settings-form" action="{{ $editing ? route('admin.pages.sections.update', [$page,$section]) : route('admin.pages.sections.store', $page) }}">
    @csrf @if($editing) @method('PUT') @endif
    <div class="admin-card mb-4"><div class="row g-3">
        <div class="col-md-6"><label class="form-label">Section Key *</label><input name="section_key" class="form-control" value="{{ old('section_key',$section->section_key) }}" placeholder="company-overview" required><div class="form-text">Gunakan huruf, angka, dash, atau underscore. Mengubah key section existing dapat memutus konten frontend.</div></div>
        <div class="col-md-6"><label class="form-label">Section Type *</label><input name="section_type" class="form-control" value="{{ old('section_type',$section->section_type) }}" placeholder="rich_text" required></div>
        <div class="col-md-6"><label class="form-label">Title</label><input name="title" class="form-control" value="{{ old('title',$section->title) }}"></div>
        <div class="col-md-6"><label class="form-label">Subtitle</label><input name="subtitle" class="form-control" value="{{ old('subtitle',$section->subtitle) }}"></div>
        <div class="col-12"><label class="form-label">Content</label><textarea name="content" class="form-control" rows="7">{{ old('content',$section->content) }}</textarea><div class="form-text">Boleh berupa plain text atau HTML sederhana sesuai kebutuhan template.</div></div>
        <div class="col-md-6"><label class="form-label">Button Text</label><input name="button_text" class="form-control" value="{{ old('button_text',$section->button_text) }}"></div>
        <div class="col-md-6"><label class="form-label">Button URL</label><input name="button_url" class="form-control" value="{{ old('button_url',$section->button_url) }}"></div>
        <div class="col-md-4"><label class="form-label">Sort Order *</label><input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order',$section->sort_order) }}" required></div>
        <div class="col-md-4 d-flex align-items-end"><div class="form-check form-switch mb-2"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active',$section->is_active))><label class="form-check-label">Section active</label></div></div>
        <div class="col-12"><label class="form-label">Settings (JSON)</label><textarea name="settings" class="form-control font-monospace" rows="5" placeholder='{"variant":"default"}'>{{ old('settings', $section->settings ? json_encode($section->settings, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) : '') }}</textarea></div>
    </div></div>
    <div class="admin-card mb-4"><div class="admin-card-header"><div><h2>Section Image</h2><p>JPG, PNG, WEBP, atau SVG maksimal 4 MB.</p></div></div><div class="admin-upload-field"><div class="admin-upload-preview admin-upload-preview-og">@if($image ?? null)<img src="{{ $image }}" alt="" data-preview-image="section-image">@else<img src="" class="d-none" alt="" data-preview-image="section-image"><i class="bi bi-image" data-preview-placeholder="section-image"></i>@endif</div><div class="flex-grow-1"><input type="file" name="image" accept="image/*" class="form-control" data-image-input data-preview-target="section-image" data-remove-target="remove_image"><input name="image_alt" class="form-control mt-2" value="{{ old('image_alt',$section->image_alt) }}" placeholder="Image alternative text"><div class="form-check mt-2"><input id="remove_image" type="checkbox" class="form-check-input" name="remove_image" value="1"><label for="remove_image" class="form-check-label">Remove current image</label></div></div></div></div>
    <div class="admin-save-bar"><p class="mb-0">Perubahan tampil pada frontend setelah disimpan.</p><button class="btn admin-primary-button"><i class="bi bi-check-lg me-1"></i>{{ $editing ? 'Update Section' : 'Create Section' }}</button></div>
</form>
@endsection
