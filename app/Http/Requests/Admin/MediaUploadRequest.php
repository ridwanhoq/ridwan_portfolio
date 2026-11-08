<?php

namespace App\Http\Requests\Admin;

use App\Services\MediaService;

class MediaUploadRequest extends AdminRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'file' => [
                'required', 'file', 'image', 'mimes:jpg,jpeg,png,webp,gif',
                'mimetypes:'.implode(',', MediaService::ALLOWED_MIME_TYPES),
                'max:'.MediaService::MAX_KILOBYTES,
                'dimensions:min_width=16,min_height=16,max_width=12000,max_height=12000',
            ],
            'alt' => ['nullable', 'string', 'max:255'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['file.mimes' => 'Upload a JPG, PNG, WebP or GIF image. SVG is not accepted for security reasons.'];
    }
}
