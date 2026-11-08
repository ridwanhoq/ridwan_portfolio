<?php

namespace App\Http\Requests\Admin;

use Closure;
use Illuminate\Http\UploadedFile;

class ResumeUploadRequest extends AdminRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'file' => [
                'required', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:5120',
                function (string $attribute, mixed $value, Closure $fail) {
                    // Check the PDF signature, not just the extension or reported type.
                    if (! $value instanceof UploadedFile || file_get_contents($value->getRealPath(), false, null, 0, 5) !== '%PDF-') {
                        $fail('The file must be a valid PDF document.');
                    }
                },
            ],
        ];
    }
}
