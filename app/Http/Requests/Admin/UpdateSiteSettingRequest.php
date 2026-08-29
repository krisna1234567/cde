<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    protected function prepareForValidation(): void
    {
        $prepared = [
            'company_name' => trim((string) $this->input('company_name')),
            'short_name' => $this->nullableTrim('short_name'),
            'tagline' => $this->nullableTrim('tagline'),
            'email' => $this->nullableTrim('email'),
            'contact_recipient_email' => $this->nullableTrim('contact_recipient_email'),
            'phone' => $this->nullableTrim('phone'),
            'whatsapp_number' => $this->nullableTrim('whatsapp_number'),
            'google_maps_embed_url' => $this->nullableTrim('google_maps_embed_url'),
            'google_maps_link' => $this->nullableTrim('google_maps_link'),
            'default_meta_title' => $this->nullableTrim('default_meta_title'),
        ];

        if ($this->has('impact_metrics_present')) {
            $prepared['impact_metrics'] = $this->preparedImpactMetrics();
        }

        $this->merge($prepared);
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:100'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'logo_alt' => ['nullable', 'string', 'max:255'],
            'remove_logo' => ['nullable', 'boolean'],
            'favicon' => ['nullable', 'file', 'mimes:png,ico,jpg,jpeg,webp', 'max:1024'],
            'remove_favicon' => ['nullable', 'boolean'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'contact_recipient_email' => ['nullable', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'regex:/^\+?[0-9\s().-]{8,30}$/'],
            'whatsapp_default_message' => ['nullable', 'string', 'max:1000'],
            'address' => ['nullable', 'string', 'max:2000'],
            'google_maps_embed_url' => ['nullable', 'url:http,https', 'max:2048'],
            'google_maps_link' => ['nullable', 'url:http,https', 'max:2048'],
            'social_links' => ['nullable', 'array'],
            'social_links.linkedin' => ['nullable', 'url:http,https', 'max:2048'],
            'social_links.instagram' => ['nullable', 'url:http,https', 'max:2048'],
            'social_links.facebook' => ['nullable', 'url:http,https', 'max:2048'],
            'social_links.twitter' => ['nullable', 'url:http,https', 'max:2048'],
            'social_links.youtube' => ['nullable', 'url:http,https', 'max:2048'],
            'social_links.telegram' => ['nullable', 'url:http,https', 'max:2048'],
            'footer_text' => ['nullable', 'string', 'max:1000'],
            'default_meta_title' => ['nullable', 'string', 'max:255'],
            'default_meta_description' => ['nullable', 'string', 'max:500'],
            'default_og_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'remove_default_og_image' => ['nullable', 'boolean'],
            'impact_metrics_present' => ['nullable', 'boolean'],
            'impact_metrics' => ['nullable', 'array', 'max:12'],
            'impact_metrics.*.value' => ['required', 'string', 'max:50'],
            'impact_metrics.*.label' => ['required', 'string', 'max:100'],
            'impact_metrics.*.sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'impact_metrics.*.is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'Nama perusahaan wajib diisi.',
            'logo.image' => 'Logo harus berupa file gambar.',
            'logo.mimes' => 'Logo hanya boleh berformat JPG, JPEG, PNG, atau WEBP.',
            'logo.max' => 'Ukuran logo maksimal 2 MB.',
            'favicon.mimes' => 'Favicon hanya boleh berformat PNG, ICO, JPG, JPEG, atau WEBP.',
            'favicon.max' => 'Ukuran favicon maksimal 1 MB.',
            'whatsapp_number.regex' => 'Format nomor WhatsApp tidak valid.',
            'google_maps_embed_url.url' => 'Google Maps Embed URL harus berupa URL HTTP/HTTPS yang valid.',
            'google_maps_link.url' => 'Google Maps Link harus berupa URL HTTP/HTTPS yang valid.',
            'default_og_image.max' => 'Ukuran default OG image maksimal 3 MB.',
            'impact_metrics.*.value.required' => 'Angka impact wajib diisi.',
            'impact_metrics.*.label.required' => 'Label impact wajib diisi.',
        ];
    }

    private function preparedImpactMetrics(): array
    {
        return collect($this->input('impact_metrics', []))
            ->filter(fn ($metric): bool => is_array($metric))
            ->map(function (array $metric): array {
                return [
                    'value' => trim((string) ($metric['value'] ?? '')),
                    'label' => trim((string) ($metric['label'] ?? '')),
                    'sort_order' => $metric['sort_order'] ?? '',
                    'is_active' => filter_var($metric['is_active'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ];
            })
            ->filter(fn (array $metric): bool => $metric['value'] !== '' || $metric['label'] !== '')
            ->values()
            ->all();
    }

    private function nullableTrim(string $key): ?string
    {
        $value = trim((string) $this->input($key));

        return $value === '' ? null : $value;
    }
}
