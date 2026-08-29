<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    protected function prepareForValidation(): void
    {
        $initials = $this->nullableTrim('initials');

        $this->merge([
            'name' => trim((string) $this->input('name')),
            'role' => $this->nullableTrim('role'),
            'quote' => trim((string) $this->input('quote')),
            'initials' => $initials ? Str::upper($initials) : null,
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'quote' => ['required', 'string', 'max:1000'],
            'initials' => ['nullable', 'string', 'max:4'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    private function nullableTrim(string $key): ?string
    {
        $value = trim((string) $this->input($key));

        return $value === '' ? null : $value;
    }
}
