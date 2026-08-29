<?php

namespace App\Http\Requests\Admin;

use App\Enums\PublishStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreMediaRequest extends FormRequest
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
            'category' => $this->nullableTrim('category'),
            'cover_image_alt' => $this->nullableTrim('cover_image_alt'),
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }

    public function rules(): array
    {
        $postId = $this->route('post')?->getKey();

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'alpha_dash', 'max:180', Rule::unique('posts', 'slug')->ignore($postId)],
            'category' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_cover_image' => ['nullable', 'boolean'],
            'cover_image_alt' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(array_column(PublishStatus::cases(), 'value'))],
            'is_featured' => ['required', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($this->input('status') !== PublishStatus::Published->value || blank($this->input('published_at'))) {
                    return;
                }

                if (Carbon::parse($this->input('published_at'))->isFuture()) {
                    $validator->errors()->add('published_at', 'Published At tidak boleh di masa depan jika status Published.');
                }
            },
        ];
    }

    private function nullableTrim(string $key): ?string
    {
        $value = trim((string) $this->input($key));

        return $value === '' ? null : $value;
    }
}
