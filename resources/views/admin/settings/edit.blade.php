@extends('layouts.admin')

@section('title', 'Site Settings')
@section('page_heading', 'Site Settings')

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="admin-settings-form">
    @csrf
    @method('PUT')

    <div class="admin-form-toolbar">
        <div>
            <h2>General website configuration</h2>
            <p>Perubahan akan langsung digunakan oleh halaman public setelah disimpan.</p>
        </div>
        <button type="submit" class="btn admin-primary-button"><i class="bi bi-floppy-fill me-2"></i>Save Changes</button>
    </div>

    <section class="admin-card mb-4">
        <div class="admin-card-header">
            <div><h2>Brand Identity</h2><p>Nama, tagline, logo, dan favicon perusahaan.</p></div>
            <span class="admin-section-icon"><i class="bi bi-buildings"></i></span>
        </div>
        <div class="row g-4">
            <div class="col-md-7">
                <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                <input type="text" id="company_name" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $setting->company_name) }}" maxlength="255" required>
                <x-admin.field-error name="company_name" />
            </div>
            <div class="col-md-5">
                <label for="short_name" class="form-label">Short Name</label>
                <input type="text" id="short_name" name="short_name" class="form-control @error('short_name') is-invalid @enderror" value="{{ old('short_name', $setting->short_name) }}" maxlength="100">
                <x-admin.field-error name="short_name" />
            </div>
            <div class="col-12">
                <label for="tagline" class="form-label">Tagline</label>
                <input type="text" id="tagline" name="tagline" class="form-control @error('tagline') is-invalid @enderror" value="{{ old('tagline', $setting->tagline) }}" maxlength="255">
                <x-admin.field-error name="tagline" />
            </div>

            <div class="col-lg-6">
                <div class="admin-upload-field">
                    <div class="admin-upload-preview admin-upload-preview-logo">
                        <img src="{{ $media['logo'] }}" alt="Current company logo" data-preview-image="logoPreview">
                    </div>
                    <div class="flex-grow-1">
                        <label for="logo" class="form-label">Company Logo</label>
                        <input type="file" id="logo" name="logo" class="form-control @error('logo') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp" data-image-input data-preview-target="logoPreview" data-remove-target="remove_logo">
                        <small class="form-text">JPG, PNG, atau WEBP. Maksimal 2 MB.</small>
                        <x-admin.field-error name="logo" />
                        <div class="form-check mt-2">
                            <input type="checkbox" id="remove_logo" name="remove_logo" value="1" class="form-check-input" @checked(old('remove_logo'))>
                            <label for="remove_logo" class="form-check-label">Remove current logo</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <label for="logo_alt" class="form-label">Logo Alt Text</label>
                <input type="text" id="logo_alt" name="logo_alt" class="form-control @error('logo_alt') is-invalid @enderror" value="{{ old('logo_alt', $setting->logo_alt) }}" maxlength="255">
                <small class="form-text">Deskripsi singkat logo untuk aksesibilitas dan SEO.</small>
                <x-admin.field-error name="logo_alt" />

                <div class="admin-upload-field mt-4">
                    <div class="admin-upload-preview admin-upload-preview-favicon">
                        @if ($media['favicon'])
                            <img src="{{ $media['favicon'] }}" alt="Current favicon" data-preview-image="faviconPreview">
                        @else
                            <span data-preview-placeholder="faviconPreview"><i class="bi bi-image"></i></span>
                            <img src="" alt="Favicon preview" class="d-none" data-preview-image="faviconPreview">
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <label for="favicon" class="form-label">Favicon</label>
                        <input type="file" id="favicon" name="favicon" class="form-control @error('favicon') is-invalid @enderror" accept=".png,.ico,.jpg,.jpeg,.webp" data-image-input data-preview-target="faviconPreview" data-remove-target="remove_favicon">
                        <small class="form-text">Disarankan berbentuk persegi, maksimal 1 MB.</small>
                        <x-admin.field-error name="favicon" />
                        <div class="form-check mt-2">
                            <input type="checkbox" id="remove_favicon" name="remove_favicon" value="1" class="form-check-input" @checked(old('remove_favicon'))>
                            <label for="remove_favicon" class="form-check-label">Remove current favicon</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-card mb-4">
        <div class="admin-card-header">
            <div><h2>Contact Information</h2><p>Informasi yang ditampilkan pada Contact, footer, dan tombol WhatsApp.</p></div>
            <span class="admin-section-icon"><i class="bi bi-person-lines-fill"></i></span>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <label for="email" class="form-label">Public Email</label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $setting->email) }}" maxlength="255">
                <x-admin.field-error name="email" />
            </div>
            <div class="col-md-6">
                <label for="contact_recipient_email" class="form-label">Contact Form Recipient</label>
                <input type="email" id="contact_recipient_email" name="contact_recipient_email" class="form-control @error('contact_recipient_email') is-invalid @enderror" value="{{ old('contact_recipient_email', $setting->contact_recipient_email) }}" maxlength="255">
                <small class="form-text">Akan digunakan untuk notifikasi form kontak pada tahap email notification.</small>
                <x-admin.field-error name="contact_recipient_email" />
            </div>
            <div class="col-md-6">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $setting->phone) }}" maxlength="50">
                <x-admin.field-error name="phone" />
            </div>
            <div class="col-md-6">
                <label for="whatsapp_number" class="form-label">WhatsApp Number</label>
                <input type="text" id="whatsapp_number" name="whatsapp_number" class="form-control @error('whatsapp_number') is-invalid @enderror" value="{{ old('whatsapp_number', $setting->whatsapp_number) }}" placeholder="628112257000">
                <small class="form-text">Gunakan kode negara, misalnya 628112257000.</small>
                <x-admin.field-error name="whatsapp_number" />
            </div>
            <div class="col-12">
                <label for="whatsapp_default_message" class="form-label">Default WhatsApp Message</label>
                <textarea id="whatsapp_default_message" name="whatsapp_default_message" class="form-control @error('whatsapp_default_message') is-invalid @enderror" rows="3" maxlength="1000">{{ old('whatsapp_default_message', $setting->whatsapp_default_message) }}</textarea>
                <x-admin.field-error name="whatsapp_default_message" />
            </div>
            <div class="col-12">
                <label for="address" class="form-label">Company Address</label>
                <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" rows="4" maxlength="2000">{{ old('address', $setting->address) }}</textarea>
                <x-admin.field-error name="address" />
            </div>
        </div>
    </section>

    <section class="admin-card mb-4">
        <div class="admin-card-header">
            <div><h2>Google Maps</h2><p>Gunakan URL embed agar peta dapat ditampilkan dengan aman tanpa menyimpan kode iframe.</p></div>
            <span class="admin-section-icon"><i class="bi bi-geo-alt-fill"></i></span>
        </div>
        <div class="row g-4">
            <div class="col-12">
                <label for="google_maps_embed_url" class="form-label">Google Maps Embed URL</label>
                <textarea id="google_maps_embed_url" name="google_maps_embed_url" class="form-control font-monospace @error('google_maps_embed_url') is-invalid @enderror" rows="3" placeholder="https://www.google.com/maps/embed?...">{{ old('google_maps_embed_url', $setting->google_maps_embed_url) }}</textarea>
                <small class="form-text">Salin nilai pada atribut <code>src</code> dari Google Maps Embed, bukan seluruh tag iframe.</small>
                <x-admin.field-error name="google_maps_embed_url" />
            </div>
            <div class="col-12">
                <label for="google_maps_link" class="form-label">Open in Google Maps Link</label>
                <input type="url" id="google_maps_link" name="google_maps_link" class="form-control @error('google_maps_link') is-invalid @enderror" value="{{ old('google_maps_link', $setting->google_maps_link) }}" placeholder="https://maps.google.com/...?">
                <x-admin.field-error name="google_maps_link" />
            </div>
        </div>
    </section>

    <section class="admin-card mb-4">
        <div class="admin-card-header">
            <div><h2>Social Media</h2><p>Kosongkan platform yang tidak ingin ditampilkan pada website.</p></div>
            <span class="admin-section-icon"><i class="bi bi-share-fill"></i></span>
        </div>
        @php
            $socialFields = [
                'linkedin' => ['LinkedIn', 'bi-linkedin'],
                'instagram' => ['Instagram', 'bi-instagram'],
                'facebook' => ['Facebook', 'bi-facebook'],
                'twitter' => ['X / Twitter', 'bi-twitter-x'],
                'youtube' => ['YouTube', 'bi-youtube'],
                'telegram' => ['Telegram', 'bi-telegram'],
            ];
        @endphp
        <div class="row g-4">
            @foreach ($socialFields as $key => [$label, $icon])
                <div class="col-md-6">
                    <label for="social_{{ $key }}" class="form-label"><i class="bi {{ $icon }} me-1"></i>{{ $label }}</label>
                    <input type="url" id="social_{{ $key }}" name="social_links[{{ $key }}]" class="form-control @error('social_links.'.$key) is-invalid @enderror" value="{{ old('social_links.'.$key, data_get($setting->social_links, $key)) }}" placeholder="https://">
                    <x-admin.field-error :name="'social_links.'.$key" />
                </div>
            @endforeach
        </div>
    </section>

    <section class="admin-card mb-4">
        <div class="admin-card-header">
            <div><h2>Footer & Default SEO</h2><p>Nilai SEO ini digunakan saat halaman tidak memiliki override sendiri.</p></div>
            <span class="admin-section-icon"><i class="bi bi-search"></i></span>
        </div>
        <div class="row g-4">
            <div class="col-12">
                <label for="footer_text" class="form-label">Footer Copyright Text</label>
                <textarea id="footer_text" name="footer_text" class="form-control @error('footer_text') is-invalid @enderror" rows="2" maxlength="1000">{{ old('footer_text', $setting->footer_text) }}</textarea>
                <x-admin.field-error name="footer_text" />
            </div>
            <div class="col-12">
                <label for="default_meta_title" class="form-label">Default Meta Title</label>
                <input type="text" id="default_meta_title" name="default_meta_title" class="form-control @error('default_meta_title') is-invalid @enderror" value="{{ old('default_meta_title', $setting->default_meta_title) }}" maxlength="255">
                <x-admin.field-error name="default_meta_title" />
            </div>
            <div class="col-12">
                <label for="default_meta_description" class="form-label">Default Meta Description</label>
                <textarea id="default_meta_description" name="default_meta_description" class="form-control @error('default_meta_description') is-invalid @enderror" rows="4" maxlength="500">{{ old('default_meta_description', $setting->default_meta_description) }}</textarea>
                <div class="d-flex justify-content-between gap-2"><small class="form-text">Disarankan sekitar 150–160 karakter.</small><small class="form-text" data-character-count="default_meta_description"></small></div>
                <x-admin.field-error name="default_meta_description" />
            </div>
            <div class="col-12">
                <div class="admin-upload-field">
                    <div class="admin-upload-preview admin-upload-preview-og">
                        <img src="{{ $media['default_og_image'] }}" alt="Current default OG image" data-preview-image="ogPreview">
                    </div>
                    <div class="flex-grow-1">
                        <label for="default_og_image" class="form-label">Default OG Image</label>
                        <input type="file" id="default_og_image" name="default_og_image" class="form-control @error('default_og_image') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp" data-image-input data-preview-target="ogPreview" data-remove-target="remove_default_og_image">
                        <small class="form-text">Disarankan 1200 × 630 px, maksimal 3 MB.</small>
                        <x-admin.field-error name="default_og_image" />
                        <div class="form-check mt-2">
                            <input type="checkbox" id="remove_default_og_image" name="remove_default_og_image" value="1" class="form-check-input" @checked(old('remove_default_og_image'))>
                            <label for="remove_default_og_image" class="form-check-label">Remove current OG image</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="admin-save-bar">
        <p class="mb-0"><i class="bi bi-info-circle me-2"></i>Pastikan menjalankan <code>php artisan storage:link</code> agar gambar upload dapat diakses.</p>
        <button type="submit" class="btn admin-primary-button"><i class="bi bi-floppy-fill me-2"></i>Save Changes</button>
    </div>
</form>
@endsection
