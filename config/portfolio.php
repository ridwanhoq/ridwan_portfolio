<?php

return [

    /*
    | Allow search engines to crawl the site (robots.txt). Defaults to on in
    | production only, so staging and local copies are never indexed.
    */
    'allow_indexing' => (bool) env('SEO_ALLOW_INDEXING', env('APP_ENV') === 'production'),

    /*
    | Default address for "new contact message" emails. Admin → Settings can
    | override it. When set, notifications are on by default.
    */
    'contact_notify_email' => env('CONTACT_NOTIFY_EMAIL') ?: null,

    /*
    | Administrator created by `php artisan db:seed` (AdminUserSeeder).
    | In production prefer `php artisan portfolio:admin`.
    */
    'admin' => [
        'name' => env('ADMIN_NAME') ?: 'Administrator',
        'email' => env('ADMIN_EMAIL') ?: 'admin@example.com',
        'password' => env('ADMIN_PASSWORD') ?: null,
    ],

];
