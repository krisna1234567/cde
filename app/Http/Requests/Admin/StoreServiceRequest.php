<?php

namespace App\Http\Requests\Admin;

use App\Enums\ServiceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'slug' => $this->nullableTrim('slug'),
            'brand' => $this->nullableTrim('brand'),
            'currency' => strtoupper($this->nullableTrim('currency') ?? 'IDR'),
            'icon' => $this->nullableTrim('icon'),
            'image_alt' => $this->nullableTrim('image_alt'),
            'is_featured' => $this->boolean('is_featured'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        $serviceId = $this->route('service')?->getKey();

        return [
            'item_type' => ['required', Rule::in(array_column(ServiceType::cases(), 'value'))],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'alpha_dash', 'max:180', Rule::unique('services', 'slug')->ignore($serviceId)],
            'brand' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
            'currency' => ['required', 'alpha', 'size:3'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
            'image_alt' => ['nullable', 'string', 'max:255'],
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
