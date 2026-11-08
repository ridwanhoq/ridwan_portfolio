<?php

namespace App\Http\Requests\Admin;

class SeoSettingRequest extends AdminRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:90'],
            'description' => ['nullable', 'string', 'max:320'],
            'og_image_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'noindex' => ['boolean'],
        ];
    }
}
