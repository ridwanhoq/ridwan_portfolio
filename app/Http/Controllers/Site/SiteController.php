<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Services\PortfolioContent;
use App\Services\Seo;
use App\Services\Settings;
use Inertia\Inertia;
use Inertia\Response;

abstract class SiteController extends Controller
{
    public function __construct(
        protected readonly PortfolioContent $content,
        protected readonly Seo $seo,
        protected readonly Settings $settings,
    ) {}

    /**
     * Render a public page with server-side SEO metadata.
     *
     * @param  array<string, mixed>  $props
     * @param  array<string, mixed>  $seo
     */
    protected function page(string $component, array $props, array $seo): Response
    {
        $pageSeo = array_diff_key($seo, array_flip(['json_ld', 'google_site_verification']));

        return Inertia::render($component, [...$props, 'seo' => $pageSeo])->withViewData(['seo' => $seo]);
    }
}
