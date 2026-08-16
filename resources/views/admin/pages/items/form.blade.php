@extends('layouts.admin')
@php($editing = $item->exists)
@section('title', ($editing ? 'Edit' : 'Add').' Section Item')
@section('page_heading', ($editing ? 'Edit' : 'Add').' Item — '.$section->section_key)

@section('content')
<div class="admin-form-toolbar"><div><h2>{{ $editing ? 'Update Item' : 'New Item' }}</h2><p>{{ $page->name }} / {{ $section->title ?: $section->section_key }}</p></div><a href="{{ route('admin.pages.edit',$page) }}" class="btn btn-outline-secondary">Back</a></div>
<form method="POST" enctype="multipart/form-data" class="admin-settings-form" action="{{ $editing ? route('admin.pages.sections.items.update',[$page,$section,$item]) : route('admin.pages.sections.items.store',[$page,$section]) }}">
    @csrf @if($editing) @method('PUT') @endif
    <div class="admin-card mb-4"><div class="row g-3">
        <div class="col-md-6"><label class="form-label">Title</label><input name="title" class="form-control" value="{{ old('title',$item->title) }}"></div>
        <div class="col-md-6"><label class="form-label">Subtitle</label><input name="subtitle" class="form-control" value="{{ old('subtitle',$item->subtitle) }}"></div>
        <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="5">{{ old('description',$item->description) }}</textarea></div>
        <div class="col-md-6"><label class="form-label">Icon Class</label><input name="icon" class="form-control" value="{{ old('icon',$item->icon) }}" placeholder="bi-sun"><div class="form-text">Contoh: Bootstrap Icons <code>bi-sun</code>.</div></div>
        <div class="col-md-6"><label class="form-label">Image Alt</label><input name="image_alt" class="form-control" value="{{ old('image_alt',$item->image_alt) }}"></div>
        <div class="col-md-6"><label class="form-label">Link Text</label><input name="link_text" class="form-control" value="{{ old('link_text',$item->link_text) }}"></div>
        <div class="col-md-6"><label class="form-label">Link URL</label><input name="link_url" class="form-control" value="{{ old('link_url',$item->link_url) }}"></div>
        <div class="col-md-4"><label class="form-label">Sort Order *</label><input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order',$item->sort_order) }}" required></div>
        <div class="col-md-4 d-flex align-items-end"><div class="form-check form-switch mb-2"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active',$item->is_active))><label class="form-check-label">Item active</label></div></div>
        <div class="col-12"><label class="form-label">Settings (JSON)</label><textarea name="settings" class="form-control font-monospace" rows="5">{{ old('settings', $item->settings ? json_encode($item->settings, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) : '') }}</textarea></div>
    </div></div>
    <div class="admin-card mb-4"><div class="admin-card-header"><div><h2>Item Image</h2><p>Opsional, maksimal 4 MB.</p></div></div><div class="admin-upload-field"><div class="admin-upload-preview">@if($image ?? null)<img src="{{ $image }}" alt="" data-preview-image="item-image">@else<img src="" class="d-none" alt="" data-preview-image="item-image"><i class="bi bi-image" data-preview-placeholder="item-image"></i>@endif</div><div class="flex-grow-1"><input type="file" name="image" accept="image/*" class="form-control" data-image-input data-preview-target="item-image" data-remove-target="remove_image"><div class="form-check mt-2"><input id="remove_image" type="checkbox" class="form-check-input" name="remove_image" value="1"><label for="remove_image" class="form-check-label">Remove current image</label></div></div></div></div>
    <div class="admin-save-bar"><p class="mb-0">Item akan mengikuti urutan pada section.</p><button class="btn admin-primary-button"><i class="bi bi-check-lg me-1"></i>{{ $editing ? 'Update Item' : 'Create Item' }}</button></div>
</form>
@endsection
