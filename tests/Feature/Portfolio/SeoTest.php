<?php

use App\Models\Project;
use App\Models\SeoSetting;
use App\Services\Settings;

beforeEach(fn () => seedPortfolio());

it('renders page metadata on the server', function () {
    $html = $this->get('/')->assertOk()->getContent();

    expect($html)
        ->toContain('<title data-inertia="">Mohammad Ridwanul Haque — Senior Software Engineer (PHP &amp; Laravel)</title>')
        ->toContain('<meta name="description" content="Senior Software Engineer with 9+ years')
        ->toContain('<link rel="canonical" href="'.url('/').'"')
        ->toContain('property="og:image"')
        ->toContain('name="twitter:card" content="summary_large_image"')
        ->toContain('<meta name="robots" content="index, follow"');
});

it('includes Person and WebSite structured data on the homepage', function () {
    $html = $this->get('/')->getContent();

    expect($html)->toContain('"@type":"Person"')
        ->toContain('"jobTitle":"Senior Software Engineer"')
        ->toContain('"@type":"WebSite"')
        ->toContain('"alumniOf"');
});

it('uses project-specific metadata on case studies', function () {
    $project = Project::query()->firstOrFail();
    $project->update(['meta_title' => 'Custom case study title', 'meta_description' => 'Custom description']);

    $html = $this->get("/projects/{$project->slug}")->getContent();

    expect($html)->toContain('Custom case study title — Mohammad Ridwanul Haque')
        ->toContain('content="Custom description"')
        ->toContain('"@type":"CreativeWork"')
        ->toContain('"@type":"BreadcrumbList"');
});

it('reflects SEO changes made in the admin', function () {
    SeoSetting::query()->where('page_key', 'about')->update(['title' => 'About me', 'description' => 'New about description', 'noindex' => true]);

    $html = $this->get('/about')->getContent();

    expect($html)->toContain('About me — Mohammad Ridwanul Haque')
        ->toContain('New about description')
        ->toContain('content="noindex, nofollow"');
});

it('outputs the Search Console verification tag when configured', function () {
    app(Settings::class)->set(['google_site_verification' => 'abc123-XYZ']);

    expect($this->get('/')->getContent())->toContain('<meta name="google-site-verification" content="abc123-XYZ">');
});

it('generates a sitemap with published projects only', function () {
    $draft = Project::query()->firstOrFail();
    $draft->update(['status' => 'draft']);

    $xml = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8')->getContent();

    expect($xml)->toContain(route('projects.index'))
        ->toContain(route('projects.show', 'sezl-corporate-website-and-cms'))
        ->not->toContain(route('projects.show', $draft->slug))
        ->not->toContain('/blog');
});

it('blocks crawlers outside production', function () {
    $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /');
});

it('allows crawlers when indexing is enabled and points to the sitemap', function () {
    config(['portfolio.allow_indexing' => true]);

    $this->get('/robots.txt')->assertSee('Disallow: /admin')->assertSee('Sitemap: '.route('sitemap'));
});
