<?php

use App\Support\ContentCache;

it('caches values until flushed', function () {
    $calls = 0;
    $resolve = function () use (&$calls) {
        return ContentCache::remember('example', function () use (&$calls) {
            return ++$calls;
        });
    };

    expect($resolve())->toBe(1)->and($resolve())->toBe(1);

    ContentCache::flush();

    expect($resolve())->toBe(2);
});
