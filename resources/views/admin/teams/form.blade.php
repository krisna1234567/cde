@extends('layouts.admin')

@php($editing = $teamMember->exists)

@section('title', ($editing ? 'Edit' : 'Add').' Team Member')
@section('page_heading', ($editing ? 'Edit' : 'Add').' Team Member')

@section('content')
    <div class="admin-form-toolbar">
        <div>
            <h2>{{ $editing ? 'Update Team Member' : 'New Team Member' }}</h2>
            <p>Kelola profil CEO dan kartu anggota tim pada section Meet Our Team di halaman About.</p>
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
                    <label class="form-label">Description (Team Card)</label>
                    <textarea name="description" maxlength="1000" class="form-control" rows="5">{{ old('description', $teamMember->description) }}</textarea>
                    <div class="form-text">Wajib untuk anggota tim biasa. Profil CEO menggunakan CEO Message.</div>
                </div>
            </div>
        </div>

        <div class="admin-card mb-4">
            <div class="admin-card-header"><div><h2>CEO Message</h2><p>Pilih satu profil CEO untuk ditampilkan di atas kartu anggota tim.</p></div></div>
            <div class="row g-3">
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_ceo" value="0">
                        <input id="is_ceo" class="form-check-input" type="checkbox" name="is_ceo" value="1" @checked(old('is_ceo', $teamMember->is_ceo))>
                        <label for="is_ceo" class="form-check-label">Display as CEO</label>
                    </div>
                    <x-admin.field-error name="is_ceo" />
                    <div class="form-text">Profil CEO menggunakan foto, nama, dan posisi di atas. Matikan pilihan ini pada CEO sebelumnya sebelum memilih profil baru.</div>
                </div>
                <div class="col-12">
                    <label for="message_title" class="form-label">Message Title</label>
                    <input id="message_title" name="message_title" class="form-control" value="{{ old('message_title', $teamMember->message_title) }}" placeholder="CEO Message" maxlength="255">
                    <x-admin.field-error name="message_title" />
                </div>
                <div class="col-12">
                    <label for="message" class="form-label">CEO Message</label>
                    <textarea id="message" name="message" class="form-control" rows="10" maxlength="10000">{{ old('message', $teamMember->message) }}</textarea>
                    <div class="form-text">Wajib diisi untuk profil CEO. Pisahkan paragraf dengan baris kosong. Description digunakan pada kartu anggota biasa.</div>
                    <x-admin.field-error name="message" />
                </div>
            </div>
        </div>

        <div class="admin-card mb-4">
            <div class="admin-card-header"><div><h2>Social Media</h2><p>Opsional, ditampilkan sebagai ikon pada kartu anggota tim. Kosongkan untuk menyembunyikan ikon.</p></div></div>
            <div class="row g-3">
                @foreach (App\Models\TeamMember::SOCIAL_PLATFORMS as $key => $platform)
                    <div class="col-md-6">
                        <label for="social_{{ $key }}" class="form-label">{{ $platform['label'] }}</label>
                        <input id="social_{{ $key }}" type="url" name="social_links[{{ $key }}]" class="form-control" value="{{ old('social_links.'.$key, data_get($teamMember->social_links, $key)) }}" placeholder="https://" maxlength="2048">
                        <x-admin.field-error :name="'social_links.'.$key" />
                    </div>
                @endforeach
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
            <p class="mb-0">CEO tampil di atas. Kartu anggota tim mengikuti Sort Order terkecil ke terbesar.</p>
            <button class="btn admin-primary-button"><i class="bi bi-check-lg me-1"></i>{{ $editing ? 'Update Team Member' : 'Create Team Member' }}</button>
        </div>
    </form>
@endsection
