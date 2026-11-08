<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class SkillRequest extends AdminRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'skill_category_id' => ['required', 'integer', 'exists:skill_categories,id'],
            'name' => ['required', 'string', 'max:80', Rule::unique('skills', 'name')
                ->where('skill_category_id', $this->integer('skill_category_id'))
                ->ignore($this->routeId())],
            'is_published' => ['boolean'],
            'sort_order' => ['integer', 'min:0', 'max:65535'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['skill_category_id' => 'category'];
    }
}
