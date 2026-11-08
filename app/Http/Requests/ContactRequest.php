<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;

class ContactRequest extends FormRequest
{
    /** Minimum seconds between rendering the form and submitting it. */
    public const MIN_SECONDS = 3;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'email' => trim((string) $this->input('email')),
            'subject' => trim((string) $this->input('subject')),
            'message' => trim((string) $this->input('message')),
        ]);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
            'subject' => ['required', 'string', 'min:3', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            // Spam protection: a hidden honeypot and a signed render timestamp.
            'website' => ['nullable', 'string', 'max:0'],
            'token' => ['required', 'string', function (string $attribute, mixed $value, Closure $fail) {
                try {
                    $renderedAt = (int) Crypt::decryptString((string) $value);
                } catch (DecryptException) {
                    $fail('The form expired. Please reload the page and try again.');

                    return;
                }

                $elapsed = time() - $renderedAt;

                if ($elapsed < self::MIN_SECONDS) {
                    $fail('That was quick. Please wait a moment and send again.');
                } elseif ($elapsed > 60 * 60 * 6) {
                    $fail('The form expired. Please reload the page and try again.');
                }
            }],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['website.max' => 'Your message could not be sent.'];
    }

    public static function freshToken(): string
    {
        return Crypt::encryptString((string) time());
    }
}
