@extends('layouts.admin')

@section('title', 'Edit '.$page->name)
@section('page_heading', 'Edit Page: '.$page->name)

@section('content')
<div class="admin-form-toolbar">
    <div><h2>Page Information</h2><p>Page key <code>{{ $page->page_key }}</code> dipertahankan karena digunakan oleh frontend.</p></div>
    <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">Back</a>
</div>

<form method="POST" action="{{ route('admin.pages.update', $page) }}" enctype="multipart/form-data" class="admin-settings-form">
    @csrf @method('PUT')
    <div class="admin-card mb-4">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Admin Name *</label><input name="name" class="form-control" value="{{ old('name', $page->name) }}" required></div>
            <div class="col-md-6"><label class="form-label">Page Title *</label><input name="title" class="form-control" value="{{ old('title', $page->title) }}" required></div>
            <div class="col-md-6"><label class="form-label">Slug *</label><input name="slug" class="form-control" value="{{ old('slug', $page->slug) }}" required></div>
            <div class="col-md-6"><label class="form-label">Navigation Label</label><input name="navigation_label" class="form-control" value="{{ old('navigation_label', $page->navigation_label) }}"></div>
            <div class="col-12"><label class="form-label">Excerpt</label><textarea name="excerpt" class="form-control" rows="3">{{ old('excerpt', $page->excerpt) }}</textarea></div>
            <div class="col-md-4"><label class="form-label">Navigation Order</label><input type="number" min="0" name="navigation_order" class="form-control" value="{{ old('navigation_order', $page->navigation_order) }}"></div>
            <div class="col-md-4 d-flex align-items-end"><div class="form-check form-switch mb-2"><input type="hidden" name="show_in_navigation" value="0"><input class="form-check-input" type="checkbox" name="show_in_navigation" value="1" @checked(old('show_in_navigation', $page->show_in_navigation))><label class="form-check-label">Show in navigation</label></div></div>
            <div class="col-md-4 d-flex align-items-end"><div class="form-check form-switch mb-2"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $page->is_active))><label class="form-check-label">Page active</label></div></div>
        </div>
    </div>

    <div class="admin-card mb-4">
        <div class="admin-card-header"><div><h2>SEO & Social Sharing</h2><p>Overrides default SEO pada Site Settings.</p></div></div>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Meta Title</label><input name="meta_title" maxlength="255" class="form-control" value="{{ old('meta_title', $page->meta_title) }}"></div>
            <div class="col-md-6"><label class="form-label">OG Title</label><input name="og_title" maxlength="255" class="form-control" value="{{ old('og_title', $page->og_title) }}"></div>
            <div class="col-md-6"><label class="form-label">Meta Description</label><textarea name="meta_description" maxlength="500" class="form-control" rows="4">{{ old('meta_description', $page->meta_description) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">OG Description</label><textarea name="og_description" maxlength="500" class="form-control" rows="4">{{ old('og_description', $page->og_description) }}</textarea></div>
            <div class="col-md-8"><label class="form-label">Canonical URL</label><input type="url" name="canonical_url" class="form-control" value="{{ old('canonical_url', $page->canonical_url) }}"></div>
            <div class="col-md-4"><label class="form-label">Robots</label><select name="robots" class="form-select">@foreach(['index,follow','index,nofollow','noindex,follow','noindex,nofollow'] as $robots)<option value="{{ $robots }}" @selected(old('robots', $page->robots) === $robots)>{{ $robots }}</option>@endforeach</select></div>
            <div class="col-12"><label class="form-label">OG Image</label><div class="admin-upload-field"><div class="admin-upload-preview admin-upload-preview-og">@if($ogImage)<img src="{{ $ogImage }}" alt="" data-preview-image="page-og">@else<img src="" alt="" class="d-none" data-preview-image="page-og"><i class="bi bi-image" data-preview-placeholder="page-og"></i>@endif</div><div class="flex-grow-1"><input type="file" name="og_image" accept="image/*" class="form-control" data-image-input data-preview-target="page-og" data-remove-target="remove_og_image"><div class="form-check mt-2"><input id="remove_og_image" class="form-check-input" type="checkbox" name="remove_og_image" value="1"><label for="remove_og_image" class="form-check-label">Remove current image</label></div></div></div></div>
        </div>
    </div>
    <div class="admin-save-bar mb-4"><p class="mb-0">Simpan pengaturan halaman sebelum mengelola section.</p><button class="btn admin-primary-button"><i class="bi bi-check-lg me-1"></i>Save Page</button></div>
</form>

<div class="admin-card">
    <div class="admin-card-header"><div><h2>Page Sections</h2><p>Urutan ini digunakan pada halaman publik.</p></div><a href="{{ route('admin.pages.sections.create', $page) }}" class="btn admin-primary-button"><i class="bi bi-plus-lg me-1"></i>Add Section</a></div>
    <div class="admin-section-list">
        @forelse($page->sections as $section)
            <div class="admin-content-block">
                <div class="admin-content-block-header">
                    <div><span class="badge text-bg-light me-2">#{{ $section->sort_order }}</span><strong>{{ $section->title ?: $section->section_key }}</strong><small class="d-block text-muted mt-1">{{ $section->section_key }} · {{ $section->section_type }} · {{ $section->is_active ? 'Active' : 'Inactive' }} · {{ $section->items->count() }} items</small></div>
                    <div class="admin-row-actions">
                        <form method="POST" action="{{ route('admin.pages.sections.move', [$page, $section, 'up']) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-secondary" title="Move up"><i class="bi bi-arrow-up"></i></button></form>
                        <form method="POST" action="{{ route('admin.pages.sections.move', [$page, $section, 'down']) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-secondary" title="Move down"><i class="bi bi-arrow-down"></i></button></form>
                        <a href="{{ route('admin.pages.sections.items.create', [$page, $section]) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-plus-lg"></i> Item</a>
                        <a href="{{ route('admin.pages.sections.edit', [$page, $section]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.pages.sections.destroy', [$page, $section]) }}" data-confirm-delete="section beserta seluruh itemnya">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
                    </div>
                </div>
                @if($section->items->isNotEmpty())
                    <div class="admin-item-list">
                    @foreach($section->items as $item)
                        <div class="admin-item-row"><div><strong>{{ $item->title ?: 'Untitled item' }}</strong><small>{{ $item->subtitle }} · #{{ $item->sort_order }} · {{ $item->is_active ? 'Active' : 'Inactive' }}</small></div><div class="admin-row-actions"><form method="POST" action="{{ route('admin.pages.sections.items.move', [$page,$section,$item,'up']) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-light"><i class="bi bi-arrow-up"></i></button></form><form method="POST" action="{{ route('admin.pages.sections.items.move', [$page,$section,$item,'down']) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-light"><i class="bi bi-arrow-down"></i></button></form><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.pages.sections.items.edit', [$page,$section,$item]) }}">Edit</a><form method="POST" action="{{ route('admin.pages.sections.items.destroy', [$page,$section,$item]) }}" data-confirm-delete="item ini">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></div></div>
                    @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="admin-empty-state"><i class="bi bi-layout-text-window d-block mb-2"></i>Belum ada section.</div>
        @endforelse
    </div>
</div>
@endsection
