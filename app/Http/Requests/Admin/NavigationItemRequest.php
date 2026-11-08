<?php

namespace App\Http\Requests\Admin;

use App\Models\NavigationItem;
use Illuminate\Validation\Rule;

class NavigationItemRequest extends AdminRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:60'],
            'url' => ['required', 'string', 'max:2048', 'regex:/^(\/[^\s]*|#[\w-]*|https?:\/\/[^\s]+|mailto:[^\s]+)$/i'],
            'location' => ['required', Rule::in(array_keys(NavigationItem::LOCATIONS))],
            'open_in_new_tab' => ['boolean'],
            'is_visible' => ['boolean'],
            'sort_order' => ['integer', 'min:0', 'max:65535'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['url.regex' => 'Use a site path (/projects), an anchor (/#about), or a full https:// URL.'];
    }
}
