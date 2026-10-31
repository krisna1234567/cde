@extends('layouts.admin')

@php
    $editing = $service->exists;
    $currentType = old('item_type', $service->item_type?->value ?? 'product');
@endphp

@section('title', ($editing ? 'Edit' : 'Add').' Product & Service')
@section('page_heading', ($editing ? 'Edit' : 'Add').' Product & Service')

@section('content')
    <div class="admin-form-toolbar">
        <div>
            <h2>{{ $editing ? 'Update Item' : 'New Item' }}</h2>
            <p>Data ini langsung digunakan oleh halaman Product & Service publik.</p>
        </div>
        <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <form method="POST" enctype="multipart/form-data" class="admin-settings-form" action="{{ $editing ? route('admin.services.update', $service) : route('admin.services.store') }}">
        @csrf
        @if($editing) @method('PUT') @endif

        <div class="admin-card mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Type *</label>
                    <select name="item_type" class="form-select" required data-item-type>
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}" @selected($currentType === $type->value)>{{ ucfirst($type->value) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Name *</label>
                    <input name="name" class="form-control" value="{{ old('name', $service->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Slug</label>
                    <input name="slug" class="form-control" value="{{ old('slug', $service->slug) }}" placeholder="auto-generated-from-name">
                    <div class="form-text">Kosongkan untuk membuat slug otomatis dari nama.</div>
                </div>

                {{-- Product-specific fields --}}
                <div class="col-md-6" data-product-field @if($currentType !== 'product') hidden @endif>
                    <label class="form-label">Brand</label>
                    <input name="brand" class="form-control" value="{{ old('brand', $service->brand) }}" placeholder="Huawei" @if($currentType !== 'product') disabled @endif>
                </div>
                <div class="col-md-6" data-product-field @if($currentType !== 'product') hidden @endif>
                    <label class="form-label" for="product_category">Product Category *</label>
                    <select id="product_category" name="product_category" class="form-select" @if($currentType !== 'product') disabled @endif>
                        <option value="">Pilih kategori produk</option>
                        @foreach (App\Models\ProductCategory::query()->orderBy('name')->get() as $category)
                            <option value="{{ $category->name }}" @selected(old('product_category', $service->product_category) === $category->name)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <x-admin.field-error name="product_category" />
                    <div class="form-text">Kategori ini digunakan sebagai filter pada katalog produk publik.</div>
                    <a href="{{ route('admin.product-categories.index') }}" class="small">Kelola kategori produk</a>
                </div>
                <div class="col-md-3" data-product-field @if($currentType !== 'product') hidden @endif>
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price', $service->price) }}" @if($currentType !== 'product') disabled @endif>
                </div>
                <div class="col-md-3" data-product-field @if($currentType !== 'product') hidden @endif>
                    <label class="form-label">Currency *</label>
                    <input name="currency" maxlength="3" class="form-control text-uppercase" value="{{ old('currency', $service->currency ?: 'IDR') }}" @if($currentType !== 'product') disabled @endif>
                </div>

                {{-- Service-specific fields --}}
                <div class="col-md-6" data-service-field @if($currentType !== 'service') hidden @endif>
                    <label class="form-label">Icon Class</label>
                    <input name="icon" class="form-control" value="{{ old('icon', $service->icon) }}" placeholder="bi-tools" @if($currentType !== 'service') disabled @endif>
                    <div class="form-text">Ikon kartu layanan di frontend. Contoh: <code>bi-tools</code>, <code>bi-gear</code>, <code>bi-lightning-charge</code>.</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Sort Order *</label>
                    <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $service->sort_order) }}" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input type="hidden" name="is_featured" value="0">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $service->is_featured))>
                        <label class="form-check-label">Featured</label>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $service->is_active))>
                        <label class="form-check-label">Active on frontend</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Short Description</label>
                    <textarea name="short_description" maxlength="1000" class="form-control" rows="3">{{ old('short_description', $service->short_description) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="7">{{ old('description', $service->description) }}</textarea>
                    <div class="form-text">Boleh berupa plain text atau HTML paragraf sederhana.</div>
                </div>
            </div>
        </div>

        <div class="admin-card mb-4" data-product-field @if($currentType !== 'product') hidden @endif>
            <div class="admin-card-header"><div><h2>Main Image</h2><p>Dipakai pada kartu dan detail product jika tersedia.</p></div></div>
            <div class="admin-upload-field">
                <div class="admin-upload-preview admin-upload-preview-og">
                    @if($media['image'])
                        <img src="{{ $media['image'] }}" alt="" data-preview-image="service-image">
                    @else
                        <img src="" class="d-none" alt="" data-preview-image="service-image"><i class="bi bi-image" data-preview-placeholder="service-image"></i>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <input type="file" name="image" accept="image/*" class="form-control" data-image-input data-preview-target="service-image" data-remove-target="remove_image" @if($currentType !== 'product') disabled @endif>
                    <input name="image_alt" class="form-control mt-2" value="{{ old('image_alt', $service->image_alt) }}" placeholder="Image alternative text" @if($currentType !== 'product') disabled @endif>
                    <div class="form-check mt-2">
                        <input id="remove_image" type="checkbox" class="form-check-input" name="remove_image" value="1" @if($currentType !== 'product') disabled @endif>
                        <label for="remove_image" class="form-check-label">Remove current image</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-card mb-4" data-service-field @if($currentType !== 'service') hidden @endif>
            <div class="admin-card-header">
                <div>
                    <h2>Service Proposal (PDF)</h2>
                    <p>File PDF ini otomatis tampil di modal dan dapat diunduh langsung oleh pengunjung saat mengeklik kartu <strong>Solar Services</strong> di halaman depan Product &amp; Service.</p>
                </div>
                <span class="admin-section-icon"><i class="bi bi-file-earmark-pdf"></i></span>
            </div>

            @if($service->hasProposal())
                <div class="alert alert-success d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <div>
                            <strong>Dokumen proposal aktif terpasang.</strong>
                            <div class="small text-muted">Pengunjung frontend dapat melihat pratinjau dan mengunduh file ini langsung dari modal kartu layanan.</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.services.proposal', $service) }}" class="btn btn-sm btn-outline-dark" target="_blank" rel="noopener">
                            <i class="bi bi-file-earmark-pdf me-1"></i>View Current Proposal
                        </a>
                        <a href="{{ route('admin.services.proposal', $service) }}?download=1" class="btn btn-sm btn-dark">
                            <i class="bi bi-download me-1"></i>Download PDF
                        </a>
                    </div>
                </div>
            @else
                <div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>
                        <strong>Belum ada dokumen proposal PDF.</strong>
                        <div class="small">Unggah dokumen proposal PDF di bawah agar pengunjung dapat membaca dan mengunduh file penawaran layanan ini.</div>
                    </div>
                </div>
            @endif

            <div class="mb-3">
                <label for="proposal" class="form-label fw-semibold">Unggah File Proposal (PDF)</label>
                <input id="proposal" type="file" name="proposal" accept="application/pdf,.pdf" class="form-control" @if($currentType !== 'service') disabled @endif>
                <div class="form-text">Format dokumen wajib PDF, ukuran maksimal 10 MB.</div>
                <x-admin.field-error name="proposal" />
            </div>

            @if($service->hasProposal())
                <div class="form-check mt-3">
                    <input id="remove_proposal" type="checkbox" class="form-check-input" name="remove_proposal" value="1" @checked(old('remove_proposal')) @if($currentType !== 'service') disabled @endif>
                    <label for="remove_proposal" class="form-check-label text-danger">Hapus proposal saat ini</label>
                </div>
            @endif
        </div>

        <div class="admin-save-bar">
            <p class="mb-0">SEO dibuat otomatis dari nama, deskripsi, slug, dan main image.</p>
            <button class="btn admin-primary-button"><i class="bi bi-check-lg me-1"></i>{{ $editing ? 'Update Item' : 'Create Item' }}</button>
        </div>
    </form>
@endsection
