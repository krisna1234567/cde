@extends('layouts.admin')

@php($editing = $testimonial->exists)

@section('title', ($editing ? 'Edit' : 'Add').' Testimonial')
@section('page_heading', ($editing ? 'Edit' : 'Add').' Testimonial')

@section('content')
<div class="admin-form-toolbar">
    <div>
        <h2>{{ $editing ? 'Update Testimonial' : 'New Testimonial' }}</h2>
        <p>Data ini langsung digunakan oleh section What Do They Say About CDE di halaman Home.</p>
    </div>
    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary">Back</a>
</div>

<form method="POST" class="admin-settings-form" action="{{ $editing ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="admin-card mb-4">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Name *</label>
                <input name="name" class="form-control" value="{{ old('name', $testimonial->name) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Initials</label>
                <input name="initials" maxlength="4" class="form-control text-uppercase" value="{{ old('initials', $testimonial->initials) }}" placeholder="EJ">
                <div class="form-text">Kosongkan untuk membuat inisial otomatis dari nama.</div>
            </div>
            <div class="col-md-8">
                <label class="form-label">Role / Company</label>
                <input name="role" class="form-control" value="{{ old('role', $testimonial->role) }}" placeholder="CEO, TheWebAgency">
            </div>
            <div class="col-md-2">
                <label class="form-label">Rating *</label>
                <select name="rating" class="form-select" required>
                    @for ($rating = 5; $rating >= 1; $rating--)
                    <option value="{{ $rating }}" @selected((int) old('rating', $testimonial->rating ?: 5) === $rating)>{{ $rating }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Sort Order *</label>
                <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $testimonial->sort_order) }}" required>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check form-switch mb-2">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $testimonial->is_active))>
                    <label class="form-check-label">Active on frontend</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label">Message *</label>
                <textarea name="quote" maxlength="1000" class="form-control" rows="5" required>{{ old('quote', $testimonial->quote) }}</textarea>
            </div>
        </div>
    </div>

    <div class="admin-save-bar">
        <p class="mb-0">Urutan testimonial mengikuti Sort Order terkecil ke terbesar.</p>
        <button class="btn admin-primary-button"><i class="bi bi-check-lg me-1"></i>{{ $editing ? 'Update Testimonial' : 'Create Testimonial' }}</button>
    </div>
</form>
@endsection