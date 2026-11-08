<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class SkillCategoryRequest extends AdminRequest
{
    protected function prepareForValidation(): void
    {
        $this->slugFrom('name');
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80'],
            'slug' => ['required_with:name', 'string', 'max:100', 'alpha_dash', Rule::unique('skill_categories', 'slug')->ignore($this->routeId())],
            'description' => ['nullable', 'string', 'max:255'],
            'is_published' => ['boolean'],
            'sort_order' => ['integer', 'min:0', 'max:65535'],
        ];
    }
}
