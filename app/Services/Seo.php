<?php

namespace App\Services;

use App\Models\Media;
use App\Models\SeoSetting;
use App\Support\ContentCache;
use Illuminate\Support\Str;

/**
 * Builds page metadata (title, description, canonical, Open Graph, Twitter,
 * structured data). Rendered server-side in the Blade root template.
 */
class Seo
{
    public function __construct(private readonly PortfolioContent $content, private readonly Settings $settings) {}

    /**
     * @param  array{title?: string|null, description?: string|null, image?: string|null, type?: string, json_ld?: list<array<string, mixed>>, noindex?: bool}  $overrides
     * @return array<string, mixed>
     */
    public function forPage(string $pageKey, array $overrides = []): array
    {
        $page = $this->pageSettings()[$pageKey] ?? null;
        $home = $this->pageSettings()['home'] ?? null;
        $site = $this->content->site();
        $siteTitle = (string) $this->settings->get('site_title', $site['name']);

        $title = $overrides['title'] ?? $page['title'] ?? $siteTitle;

        if ($pageKey !== 'home' && ! str_contains($title, $site['name'])) {
            $title = "{$title} — {$site['name']}";
        }

        $description = $overrides['description'] ?? $page['description'] ?? $home['description'] ?? null;
        $image = $overrides['image'] ?? $page['image'] ?? $home['image'] ?? asset('images/og-default.png');

        return [
            'title' => Str::limit($title, 90, ''),
            'description' => $description ? Str::limit($description, 300) : null,
            'canonical' => url()->current(),
            'image' => $image,
            'type' => $overrides['type'] ?? 'website',
            'site_name' => $site['name'],
            'robots' => ($overrides['noindex'] ?? $page['noindex'] ?? false) ? 'noindex, nofollow' : 'index, follow',
            'json_ld' => $overrides['json_ld'] ?? [],
            'google_site_verification' => $this->settings->get('google_site_verification'),
        ];
    }

    /** @return array<string, mixed> */
    public function personSchema(): array
    {
        $site = $this->content->site();
        $profile = $this->content->profile();

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $profile['full_name'],
            'jobTitle' => $profile['designation'],
            'description' => $profile['summary'] ? Str::limit($profile['summary'], 300) : null,
            'url' => url('/'),
            'image' => $profile['photo']['url'] ?? null,
            'email' => $profile['email'] ? 'mailto:'.$profile['email'] : null,
            'address' => $profile['location'] ? ['@type' => 'PostalAddress', 'addressLocality' => $profile['location']] : null,
            'sameAs' => collect(is_array($site['socials']) ? $site['socials'] : [])->where('platform', '!=', 'email')->pluck('url')->values()->all() ?: null,
            'knowsAbout' => collect($this->content->skills())->pluck('skills')->flatten()->take(25)->values()->all() ?: null,
            'alumniOf' => collect($this->content->education())
                ->map(fn ($e) => ['@type' => 'CollegeOrUniversity', 'name' => $e['institution']])
                ->unique('name')->values()->all() ?: null,
        ]);
    }

    /** @return array<string, mixed> */
    public function websiteSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $this->content->site()['name'],
            'url' => url('/'),
        ];
    }

    /**
     * @param  list<array{name: string, url: string}>  $items
     * @return array<string, mixed>
     */
    public function breadcrumbSchema(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(fn ($item, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ])->all(),
        ];
    }

    /** @return array<string, array{title: string|null, description: string|null, image: string|null, noindex: bool}> */
    private function pageSettings(): array
    {
        return ContentCache::remember('seo', fn () => SeoSetting::query()->with('ogImage')->get()
            ->mapWithKeys(fn (SeoSetting $s) => [$s->page_key => [
                'title' => $s->title,
                'description' => $s->description,
                'image' => $s->ogImage instanceof Media ? $s->ogImage->url : null,
                'noindex' => $s->noindex,
            ]])->all());
    }
}
