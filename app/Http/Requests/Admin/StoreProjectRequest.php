<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim((string) $this->input('title')),
            'slug' => $this->nullableTrim('slug'),
            'client_name' => $this->nullableTrim('client_name'),
            'category' => $this->nullableTrim('category'),
            'capacity' => $this->nullableTrim('capacity'),
            'cover_image_alt' => $this->nullableTrim('cover_image_alt'),
            'main_image_alt' => $this->nullableTrim('main_image_alt'),
            'secondary_image_alt' => $this->nullableTrim('secondary_image_alt'),
            'client_logo_alt' => $this->nullableTrim('client_logo_alt'),
            'project_url' => $this->nullableTrim('project_url'),
            'is_featured' => $this->boolean('is_featured'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        $projectId = $this->route('project')?->getKey();

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'alpha_dash', 'max:180', Rule::unique('portfolios', 'slug')->ignore($projectId)],
            'client_name' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'project_date' => ['nullable', 'date'],
            'capacity' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:1000'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'overview' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_cover_image' => ['nullable', 'boolean'],
            'cover_image_alt' => ['nullable', 'string', 'max:255'],
            'main_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_main_image' => ['nullable', 'boolean'],
            'main_image_alt' => ['nullable', 'string', 'max:255'],
            'secondary_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_secondary_image' => ['nullable', 'boolean'],
            'secondary_image_alt' => ['nullable', 'string', 'max:255'],
            'client_logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'remove_client_logo' => ['nullable', 'boolean'],
            'client_logo_alt' => ['nullable', 'string', 'max:255'],
            'project_url' => ['nullable', 'url:http,https', 'max:2048'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'is_featured' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    private function nullableTrim(string $key): ?string
    {
        $value = trim((string) $this->input($key));

        return $value === '' ? null : $value;
    }
}
