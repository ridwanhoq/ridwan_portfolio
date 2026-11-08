<?php

namespace App\Http\Requests\Admin;

class PageSectionRequest extends AdminRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:60'],
            'heading' => ['nullable', 'string', 'max:160'],
            'intro' => ['nullable', 'string', 'max:1000'],
            'is_enabled' => ['boolean'],
            'sort_order' => ['integer', 'min:0', 'max:65535'],
        ];
    }
}
