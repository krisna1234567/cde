<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientLogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'logo_alt' => $this->nullableTrim('logo_alt'),
            'website_url' => $this->nullableTrim('website_url'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        $clientLogo = $this->route('clientLogo');

        return [
            'name' => ['required', 'string', 'max:255'],
            'logo' => [$clientLogo?->exists ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
            'remove_logo' => ['nullable', 'boolean'],
            'logo_alt' => ['nullable', 'string', 'max:255'],
            'website_url' => ['nullable', 'url:http,https', 'max:2048'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.required' => 'Logo klien wajib diunggah.',
            'logo.image' => 'Logo klien harus berupa file gambar.',
            'logo.mimes' => 'Logo klien hanya boleh berformat JPG, JPEG, PNG, WEBP, atau SVG.',
            'website_url.url' => 'Website URL harus berupa URL HTTP/HTTPS yang valid.',
        ];
    }

    private function nullableTrim(string $key): ?string
    {
        $value = trim((string) $this->input($key));

        return $value === '' ? null : $value;
    }
}
