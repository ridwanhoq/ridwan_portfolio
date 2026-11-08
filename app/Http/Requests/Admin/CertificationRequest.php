<?php

namespace App\Http\Requests\Admin;

use App\Models\Certification;
use Illuminate\Validation\Rule;

class CertificationRequest extends AdminRequest
{
    protected function prepareForValidation(): void
    {
        $this->monthToDate('issued_on', 'expires_on');
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'issuer' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(Certification::TYPES))],
            'issued_on' => ['nullable', 'date'],
            'expires_on' => ['nullable', 'date', 'after_or_equal:issued_on'],
            'credential_id' => ['nullable', 'string', 'max:255'],
            'credential_url' => ['nullable', 'string', 'max:2048', self::SAFE_URL],
            'description' => ['nullable', 'string', 'max:255'],
            'is_published' => ['boolean'],
            'sort_order' => ['integer', 'min:0', 'max:65535'],
        ];
    }
}
