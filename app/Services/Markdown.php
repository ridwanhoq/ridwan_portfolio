<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Renders admin-authored Markdown into safe HTML.
 *
 * Raw HTML is stripped and javascript:/data: links are removed, so the output
 * can be rendered with v-html without XSS risk.
 */
class Markdown
{
    public static function toHtml(?string $markdown): ?string
    {
        if ($markdown === null || trim($markdown) === '') {
            return null;
        }

        return (string) Str::markdown($markdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 20,
        ]);
    }

    /**
     * Plain-text version, e.g. for meta descriptions.
     */
    public static function toText(?string $markdown, int $limit = 160): string
    {
        $html = self::toHtml($markdown) ?? '';

        return Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($html)) ?? ''), $limit);
    }
}
