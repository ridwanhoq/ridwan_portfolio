<?php

namespace App\Http\Requests\Admin;

use App\Models\Experience;
use Illuminate\Validation\Rule;

class ExperienceRequest extends AdminRequest
{
    protected function prepareForValidation(): void
    {
        $this->monthToDate('start_date', 'end_date');
        $this->cleanList('responsibilities', 'contributions');

        if ($this->boolean('is_current')) {
            $this->merge(['end_date' => null]);
        }
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'company' => ['required', 'string', 'max:255'],
            'company_url' => ['nullable', 'string', 'max:2048', self::SAFE_URL],
            'title' => ['required', 'string', 'max:255'],
            'employment_type' => ['required', Rule::in(array_keys(Experience::EMPLOYMENT_TYPES))],
            'location' => ['nullable', 'string', 'max:255'],
            'focus' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date', 'required_unless:is_current,true,1'],
            'is_current' => ['boolean'],
            'description' => ['nullable', 'string', 'max:10000'],
            'responsibilities' => ['array', 'max:30'],
            'responsibilities.*' => ['string', 'max:500'],
            'contributions' => ['array', 'max:30'],
            'contributions.*' => ['string', 'max:500'],
            'technology_ids' => ['array'],
            'technology_ids.*' => ['integer', 'exists:technologies,id'],
            'is_published' => ['boolean'],
            'sort_order' => ['integer', 'min:0', 'max:65535'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['end_date.required_unless' => 'Add an end date or mark this as your current role.'];
    }
}
