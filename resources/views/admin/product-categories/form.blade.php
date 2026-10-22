@extends('layouts.admin')

@php($editing = $category->exists)
@section('title', ($editing ? 'Edit' : 'Add').' Product Category')
@section('page_heading', ($editing ? 'Edit' : 'Add').' Product Category')

@section('content')
    <div class="admin-form-toolbar">
        <div><h2>{{ $editing ? 'Edit Category' : 'New Category' }}</h2><p>Nama kategori akan tampil di form produk dan filter katalog.</p></div>
        <a href="{{ route('admin.product-categories.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>
    <form method="POST" action="{{ $editing ? route('admin.product-categories.update', $category) : route('admin.product-categories.store') }}">
        @csrf
        @if($editing) @method('PUT') @endif
        <div class="admin-card mb-4">
            <label class="form-label" for="category-name">Category Name *</label>
            <input id="category-name" name="name" class="form-control" maxlength="80" value="{{ old('name', $category->name) }}" placeholder="Contoh: Solar Module" required>
            <x-admin.field-error name="name" />
            @if($editing)<p class="form-text mb-0">Perubahan nama otomatis diterapkan pada semua produk yang menggunakan kategori ini.</p>@endif
        </div>
        <div class="admin-save-bar">
            <p class="mb-0">Kategori langsung tersedia di katalog publik.</p>
            <button class="btn admin-primary-button"><i class="bi bi-check-lg me-1"></i>{{ $editing ? 'Update Category' : 'Create Category' }}</button>
        </div>
    </form>
@endsection
