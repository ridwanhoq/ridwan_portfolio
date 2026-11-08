<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

/**
 * Versioned cache for public content. Bumping the version invalidates every
 * cached entry at once without needing cache tags (works on any cache store).
 */
class ContentCache
{
    private const VERSION_KEY = 'content-cache:version';

    private const TTL_SECONDS = 86400;

    /**
     * @template T
     *
     * @param  Closure(): T  $callback
     * @return T
     */
    public static function remember(string $key, Closure $callback): mixed
    {
        return Cache::remember(self::key($key), self::TTL_SECONDS, $callback);
    }

    public static function flush(): void
    {
        Cache::forever(self::VERSION_KEY, self::version() + 1);
    }

    private static function key(string $key): string
    {
        return 'content:'.self::version().':'.$key;
    }

    private static function version(): int
    {
        return (int) Cache::get(self::VERSION_KEY, 1);
    }
}
