<?php

use App\Models\Experience;
use App\Models\PageSection;
use App\Models\Post;
use App\Models\Project;
use App\Services\Settings;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(fn () => seedPortfolio());

it('renders the homepage with database-driven content', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('site/Home')
            ->where('profile.full_name', 'Mohammad Ridwanul Haque')
            ->where('profile.hero_title', 'Building Reliable Software. Solving Complex Problems.')
            ->has('experiences', 4)
            ->has('projects', 4)
            ->has('skills', 8)
            ->has('principles', 4)
            ->has('education', 2)
            ->where('site.availability.enabled', true)
            ->has('contact.token'));
});

it('renders every public page', function (string $url, string $component) {
    $this->get($url)->assertOk()->assertInertia(fn (Assert $page) => $page->component($component));
})->with([
    ['/about', 'site/About'],
    ['/projects', 'site/projects/Index'],
    ['/projects/itlms-training-and-learning-management-system', 'site/projects/Show'],
    ['/resume', 'site/Resume'],
    ['/contact', 'site/Contact'],
    ['/privacy', 'site/Privacy'],
]);

it('lists experience most recent first and hides unpublished entries', function () {
    Experience::query()->where('company', 'Southtech Limited')->update(['is_published' => false]);

    $this->get('/')->assertInertia(fn (Assert $page) => $page
        ->has('experiences', 3)
        ->where('experiences.0.company', 'Riseup Labs')
        ->where('experiences.0.is_current', true)
        ->where('experiences.2.company', 'Moon Networks Ltd.'));
});

it('only shows published projects', function () {
    $project = Project::query()->firstOrFail();
    $project->update(['status' => 'draft']);

    $this->get("/projects/{$project->slug}")->assertNotFound();
    $this->get('/projects')->assertInertia(fn (Assert $page) => $page->has('projects', 3));
});

it('does not publish projects scheduled for the future', function () {
    $project = Project::query()->firstOrFail();
    $project->update(['published_at' => now()->addDay()]);

    $this->get("/projects/{$project->slug}")->assertNotFound();
});

it('only includes case study sections that have content', function () {
    $this->get('/projects/sezl-corporate-website-and-cms')->assertInertia(fn (Assert $page) => $page
        ->has('project.sections', 3)
        ->where('project.sections.0.key', 'overview')
        ->where('project.sections.2.key', 'architecture'));
});

it('hides disabled homepage sections', function () {
    PageSection::query()->where('key', 'skills')->update(['is_enabled' => false]);

    $this->get('/')->assertInertia(fn (Assert $page) => $page
        ->where('skills', [])
        ->where('sections', fn ($sections) => collect($sections)->pluck('key')->doesntContain('skills')));
});

it('respects homepage section ordering', function () {
    PageSection::query()->where('key', 'contact')->update(['sort_order' => 1]);

    $this->get('/')->assertInertia(fn (Assert $page) => $page->where('sections.0.key', 'contact'));
});

it('does not expose unpublished social links', function () {
    $this->get('/')->assertInertia(fn (Assert $page) => $page->where('site.socials', []));
});

it('renders a branded 404 page', function () {
    $this->get('/does-not-exist')
        ->assertNotFound()
        ->assertInertia(fn (Assert $page) => $page->component('site/Error')->where('status', 404));
});

it('keeps the blog private until it is enabled', function () {
    Post::query()->create(['title' => 'Hello', 'slug' => 'hello', 'body' => 'Body text', 'status' => 'published', 'published_at' => now()->subDay()]);

    $this->get('/blog')->assertNotFound();

    app(Settings::class)->set(['blog_enabled' => true]);

    $this->get('/blog')->assertOk()->assertInertia(fn (Assert $page) => $page->component('site/blog/Index')->has('posts', 1));
    $this->get('/blog/hello')->assertOk();
});

it('hides draft blog posts', function () {
    app(Settings::class)->set(['blog_enabled' => true]);
    Post::query()->create(['title' => 'Draft', 'slug' => 'draft', 'body' => 'x', 'status' => 'draft']);

    $this->get('/blog/draft')->assertNotFound();
});

it('downloads a generated PDF résumé', function () {
    $response = $this->get('/resume/download');

    $response->assertOk()->assertHeader('Content-Type', 'application/pdf');
    expect($response->headers->get('Content-Disposition'))->toContain('mohammad-ridwanul-haque-resume.pdf')
        ->and(substr((string) $response->getContent(), 0, 5))->toBe('%PDF-');
});

it('sends security headers', function () {
    $this->get('/')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
});
