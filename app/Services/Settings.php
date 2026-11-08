<?php

namespace App\Services;

use App\Models\WebsiteSetting;
use App\Support\ContentCache;

/**
 * Key/value website settings with typed defaults.
 */
class Settings
{
    /**
     * Every supported setting and its default value.
     */
    public const DEFAULTS = [
        // General
        'site_title' => 'Mohammad Ridwanul Haque — Senior Software Engineer',
        'footer_note' => '',
        'google_site_verification' => null,
        // Contact
        'contact_enabled' => true,
        'contact_intro' => 'Have a role, project or contract in mind? Send a message and I will reply within two working days.',
        'contact_success_message' => 'Thanks for your message. I will get back to you soon.',
        'contact_notify_enabled' => false,
        'contact_notify_email' => null,
        // Blog
        'blog_enabled' => false,
        // Projects
        'projects_show_filters' => true,
        // Privacy policy (Markdown)
        'privacy_policy' => null,
        // Résumé
        'resume_source' => 'generated',
        'resume_path' => null,
        'resume_original_name' => null,
        'resume_uploaded_at' => null,
    ];

    /** @var array<string, mixed>|null */
    private ?array $loaded = null;

    /**
     * Defaults, with environment-driven values applied.
     *
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        $notifyEmail = config('portfolio.contact_notify_email');

        return [
            ...self::DEFAULTS,
            'contact_notify_email' => $notifyEmail,
            'contact_notify_enabled' => filled($notifyEmail),
        ];
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->all();

        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->loaded ??= ContentCache::remember('settings', fn () => array_merge(
            self::defaults(),
            WebsiteSetting::query()->pluck('value', 'key')->all(),
        ));
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function set(array $values): void
    {
        foreach ($values as $key => $value) {
            if (! array_key_exists($key, self::DEFAULTS)) {
                continue;
            }

            WebsiteSetting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->loaded = null;
    }
}
