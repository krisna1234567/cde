<?php

namespace App\Http\Requests\Admin;

use App\Models\TeamMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'is_ceo' => $this->boolean('is_ceo'),
            'message_title' => $this->nullableTrim('message_title'),
            'message' => $this->nullableTrim('message'),
        ]);
    }

    public function rules(): array
    {
        $teamMember = $this->route('teamMember');
        $photoRequired = ! ($teamMember?->exists && filled($teamMember->photo_path));

        return [
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'description' => ['nullable', Rule::requiredIf(! $this->boolean('is_ceo')), 'string', 'max:1000'],
            'photo' => [$photoRequired ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['required', 'boolean'],
            'is_ceo' => [
                'required', 'boolean',
                Rule::when($this->boolean('is_ceo'), [
                    Rule::unique('team_members', 'is_ceo')->whereNull('deleted_at')->ignore($teamMember?->id),
                ]),
            ],
            'message_title' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', Rule::requiredIf($this->boolean('is_ceo')), 'string', 'max:10000'],
            'social_links' => ['nullable', 'array:'.implode(',', array_keys(TeamMember::SOCIAL_PLATFORMS))],
            'social_links.*' => ['nullable', 'url:http,https', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'is_ceo.unique' => 'Sudah ada profil CEO. Edit profil tersebut atau nonaktifkan pilihan CEO pada profil sebelumnya.',
            'message.required' => 'CEO Message wajib diisi untuk profil CEO.',
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
