<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'position' => $this->nullableTrim('position'),
            'description' => trim((string) $this->input('description')),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        $teamMember = $this->route('teamMember');
        $photoRequired = ! ($teamMember?->exists && filled($teamMember->photo_path));

        return [
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'photo' => [$photoRequired ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'photo.required' => 'Foto team member wajib diunggah.',
            'photo.image' => 'Foto team member harus berupa file gambar.',
            'photo.mimes' => 'Foto team member hanya boleh berformat JPG, JPEG, PNG, WEBP, atau SVG.',
        ];
    }

    private function nullableTrim(string $key): ?string
    {
        $value = trim((string) $this->input($key));

        return $value === '' ? null : $value;
    }
}
