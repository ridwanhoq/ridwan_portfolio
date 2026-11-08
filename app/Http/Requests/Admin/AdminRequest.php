<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

abstract class AdminRequest extends FormRequest
{
    /**
     * Rule for links rendered as href: blocks javascript:, data: and similar schemes.
     */
    public const SAFE_URL = 'regex:/^https?:\/\/[^\s]+$/i';

    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    protected function routeId(): ?int
    {
        $id = $this->route('id');

        return $id === null ? null : (int) $id;
    }

    /**
     * Fill an empty slug from another field.
     */
    protected function slugFrom(string $field, string $slugField = 'slug'): void
    {
        $slug = trim((string) $this->input($slugField));

        $this->merge([$slugField => Str::slug($slug !== '' ? $slug : (string) $this->input($field))]);
    }

    /**
     * Convert month inputs (YYYY-MM) to dates.
     */
    protected function monthToDate(string ...$fields): void
    {
        foreach ($fields as $field) {
            $value = $this->input($field);

            if (is_string($value) && preg_match('/^\d{4}-\d{2}$/', $value)) {
                $this->merge([$field => $value.'-01']);
            }
        }
    }

    /**
     * Trim list inputs and drop blank lines.
     */
    protected function cleanList(string ...$fields): void
    {
        foreach ($fields as $field) {
            $value = $this->input($field);

            if (is_string($value)) {
                $value = preg_split('/\r\n|\r|\n/', $value) ?: [];
            }

            if (is_array($value)) {
                $this->merge([$field => array_values(array_filter(array_map(
                    fn ($v) => is_string($v) ? trim($v) : $v,
                    $value,
                ), fn ($v) => is_string($v) && $v !== ''))]);
            }
        }
    }
}
