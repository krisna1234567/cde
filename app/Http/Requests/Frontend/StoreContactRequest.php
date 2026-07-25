<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'first_name' => trim((string) $this->input('first_name')),
            'last_name' => trim((string) $this->input('last_name')),
            'email' => strtolower(trim((string) $this->input('email'))),
            'phone' => filled(trim((string) $this->input('phone'))) ? trim((string) $this->input('phone')) : null,
            'message' => trim((string) $this->input('message')),
        ]);
    }

    public function rules(): array
    {
        return [
            'first_name' => ['bail', 'required', 'string', 'max:60'],
            'last_name' => ['bail', 'required', 'string', 'max:60'],
            'email' => ['bail', 'required', 'email:rfc', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+()\-\s]+$/'],
            'message' => ['bail', 'required', 'string', 'min:10', 'max:2000'],
            'website' => ['nullable', 'max:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'phone.regex' => 'Please enter a valid phone number.',
            'message.required' => 'Message is required.',
            'message.min' => 'Message must contain at least 10 characters.',
            'message.max' => 'Message may not exceed 2,000 characters.',
            'website.max' => 'The form could not be submitted.',
        ];
    }
}
