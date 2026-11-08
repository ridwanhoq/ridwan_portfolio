<?php

namespace App\Http\Requests\Admin;

class ProfileRequest extends AdminRequest
{
    protected function prepareForValidation(): void
    {
        $this->cleanList('hero_technologies', 'languages');
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $markdown = ['nullable', 'string', 'max:10000'];

        return [
            'full_name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:60'],
            'designation' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'hero_title' => ['required', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:1000'],
            'summary' => ['nullable', 'string', 'max:2000'],
            'location' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'show_phone' => ['boolean'],
            'work_preference' => ['nullable', 'string', 'max:255'],
            'years_experience' => ['nullable', 'integer', 'between:0,60'],
            'years_primary_stack' => ['nullable', 'integer', 'between:0,60'],
            'primary_stack_label' => ['nullable', 'string', 'max:60'],
            'hero_technologies' => ['array', 'max:12'],
            'hero_technologies.*' => ['string', 'max:40'],
            'availability_enabled' => ['boolean'],
            'availability_text' => ['nullable', 'required_if_accepted:availability_enabled', 'string', 'max:120'],
            'photo_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'about_intro' => $markdown,
            'philosophy' => $markdown,
            'career_overview' => $markdown,
            'specialization' => $markdown,
            'interests' => $markdown,
            'collaboration' => $markdown,
            'languages' => ['array', 'max:10'],
            'languages.*' => ['string', 'max:80'],
        ];
    }
}
