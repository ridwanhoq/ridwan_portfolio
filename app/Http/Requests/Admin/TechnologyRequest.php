<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class TechnologyRequest extends AdminRequest
{
    protected function prepareForValidation(): void
    {
        $this->slugFrom('name');
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80', Rule::unique('technologies', 'name')->ignore($this->routeId())],
            'slug' => ['required_with:name', 'string', 'max:100', 'alpha_dash', Rule::unique('technologies', 'slug')->ignore($this->routeId())],
            'url' => ['nullable', 'string', 'max:2048', self::SAFE_URL],
            'sort_order' => ['integer', 'min:0', 'max:65535'],
        ];
    }
}
