<?php

namespace App\Http\Requests\Admin;

use App\Models\SocialLink;
use Illuminate\Validation\Rule;

class SocialLinkRequest extends AdminRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'platform' => ['required', Rule::in(array_keys(SocialLink::PLATFORMS))],
            'label' => ['required', 'string', 'max:60'],
            'url' => ['required', 'string', 'max:2048', 'regex:/^(https?:\/\/[^\s]+|mailto:[^\s@]+@[^\s@]+)$/i'],
            'show_in_hero' => ['boolean'],
            'is_published' => ['boolean'],
            'sort_order' => ['integer', 'min:0', 'max:65535'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['url.regex' => 'Use a full https:// URL or a mailto: address.'];
    }
}
