<?php

namespace App\Models\Concerns;

use App\Support\ContentCache;

/**
 * Invalidates cached public content whenever a content model changes.
 */
trait FlushesContentCache
{
    public static function bootFlushesContentCache(): void
    {
        static::saved(fn () => ContentCache::flush());
        static::deleted(fn () => ContentCache::flush());
    }
}
