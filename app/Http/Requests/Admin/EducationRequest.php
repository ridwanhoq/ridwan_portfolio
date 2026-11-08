<?php

namespace App\Http\Requests\Admin;

class EducationRequest extends AdminRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'institution' => ['required', 'string', 'max:255'],
            'degree' => ['required', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'start_year' => ['nullable', 'integer', 'between:1950,2100'],
            'graduation_year' => ['nullable', 'integer', 'between:1950,2100', 'gte:start_year'],
            'grade' => ['nullable', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_published' => ['boolean'],
            'sort_order' => ['integer', 'min:0', 'max:65535'],
        ];
    }
}
