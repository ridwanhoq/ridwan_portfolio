<?php

namespace App\Http\Requests\Admin;

use App\Models\Principle;
use Illuminate\Validation\Rule;

class PrincipleRequest extends AdminRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', Rule::in(array_keys(Principle::ICONS))],
            'body' => ['required', 'string', 'max:5000'],
            'is_published' => ['boolean'],
            'sort_order' => ['integer', 'min:0', 'max:65535'],
        ];
    }
}
