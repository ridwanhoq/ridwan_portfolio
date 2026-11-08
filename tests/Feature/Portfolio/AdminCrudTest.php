<?php

use App\Models\ContactMessage;
use App\Models\Experience;
use App\Models\Media;
use App\Models\NavigationItem;
use App\Models\PageSection;
use App\Models\Profile;
use App\Models\Project;
use App\Models\SeoSetting;
use App\Models\Technology;
use App\Services\Settings;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    seedPortfolio();
    $this->actingAs(admin());
});

it('searches, filters, sorts and paginates resources', function () {
    $this->get('/admin/projects?search=SEZL')
        ->assertInertia(fn (Assert $page) => $page->component('admin/resource/Index')->has('items.data', 1));

    $this->get('/admin/technologies?sort=name&direction=desc')
        ->assertInertia(fn (Assert $page) => $page->where('query.sort', 'name')->where('query.direction', 'desc')->where('items.per_page', 25)->has('items.data', 25));

    $this->get('/admin/projects?status=draft')
        ->assertInertia(fn (Assert $page) => $page->has('items.data', 0));
});

it('ignores unknown sort columns', function () {
    $this->get('/admin/projects?sort=password&direction=asc')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('query.sort', 'sort_order'));
});

it('creates, updates and deletes an experience', function () {
    $tech = Technology::query()->where('name', 'Laravel')->value('id');

    $this->post('/admin/experiences', [
        'company' => 'Acme', 'title' => 'Lead Engineer', 'employment_type' => 'contract',
        'start_date' => '2025-01', 'is_current' => true, 'responsibilities' => ['Build APIs', '  ', 'Review code'],
        'technology_ids' => [$tech], 'is_published' => true, 'sort_order' => 0,
    ])->assertRedirect('/admin/experiences')->assertSessionHasNoErrors();

    $experience = Experience::query()->where('company', 'Acme')->sole();
    expect($experience->responsibilities)->toBe(['Build APIs', 'Review code'])
        ->and($experience->start_date->format('Y-m-d'))->toBe('2025-01-01')
        ->and($experience->technologies->pluck('id')->all())->toBe([$tech]);

    $this->put("/admin/experiences/{$experience->id}", [
        'company' => 'Acme Ltd', 'title' => 'Lead Engineer', 'employment_type' => 'contract',
        'start_date' => '2025-01', 'end_date' => '2025-06', 'is_current' => false, 'is_published' => true, 'sort_order' => 0,
    ])->assertSessionHasNoErrors();

    expect($experience->fresh()->company)->toBe('Acme Ltd')
        ->and($experience->fresh()->technologies)->toHaveCount(0);

    $this->delete("/admin/experiences/{$experience->id}")->assertRedirect('/admin/experiences');
    expect(Experience::query()->find($experience->id))->toBeNull();
});

it('requires an end date unless the role is current', function () {
    $this->post('/admin/experiences', [
        'company' => 'Acme', 'title' => 'Dev', 'employment_type' => 'full_time', 'start_date' => '2024-01', 'is_current' => false,
    ])->assertSessionHasErrors('end_date');
});

it('saves a project with technologies, gallery and links in one transaction', function () {
    $media = Media::query()->create(['disk' => 'public', 'directory' => 'media', 'filename' => 'a.png', 'original_name' => 'a.png', 'mime_type' => 'image/png', 'size' => 10]);
    $techs = Technology::query()->limit(2)->pluck('id')->all();

    $this->post('/admin/projects', [
        'title' => 'Booking Platform', 'slug' => '', 'summary' => 'A booking platform.', 'status' => 'published',
        'technology_ids' => $techs, 'thumbnail_media_id' => $media->id,
        'images' => [['media_id' => $media->id, 'caption' => 'Dashboard', 'kind' => 'screenshot'], ['media_id' => null, 'kind' => 'diagram']],
        'links' => [['label' => 'Docs', 'url' => 'https://example.com/docs'], ['label' => 'empty', 'url' => '']],
        'overview' => '## Overview', 'is_featured' => true, 'sort_order' => 0,
    ])->assertSessionHasNoErrors();

    $project = Project::query()->where('slug', 'booking-platform')->sole();
    expect($project->technologies)->toHaveCount(2)
        ->and($project->images)->toHaveCount(1)
        ->and($project->links)->toBe([['label' => 'Docs', 'url' => 'https://example.com/docs']])
        ->and($project->published_at)->not->toBeNull();

    $this->get('/projects/booking-platform')->assertOk();
});

it('rejects unsafe URLs that could run scripts', function (string $url) {
    $this->post('/admin/projects', ['title' => 'X', 'summary' => 'Y', 'status' => 'draft', 'live_url' => $url])
        ->assertSessionHasErrors('live_url');
})->with(['javascript:alert(1)', 'data:text/html,<script>alert(1)</script>', 'ftp://example.com']);

it('enforces unique slugs', function () {
    $this->post('/admin/projects', ['title' => 'X', 'slug' => 'sezl-corporate-website-and-cms', 'summary' => 'Y', 'status' => 'draft'])
        ->assertSessionHasErrors('slug');
});

it('toggles allowed boolean fields only', function () {
    $project = Project::query()->firstOrFail();

    $this->patch("/admin/projects/{$project->id}/toggle", ['field' => 'is_featured'])->assertRedirect();
    expect($project->fresh()->is_featured)->toBeFalse();

    $this->patch("/admin/projects/{$project->id}/toggle", ['field' => 'status'])->assertSessionHasErrors('field');
});

it('reflects admin changes on the public site immediately', function () {
    $this->get('/'); // warm the cache

    $this->put('/admin/profile', [
        ...Profile::query()->firstOrFail()->only(['full_name', 'designation']),
        'hero_title' => 'A brand new headline', 'availability_enabled' => false,
    ])->assertSessionHasNoErrors();

    $this->get('/')->assertInertia(fn (Assert $page) => $page
        ->where('profile.hero_title', 'A brand new headline')
        ->where('site.availability.enabled', false));
});

it('requires availability text when the badge is enabled', function () {
    $this->put('/admin/profile', ['full_name' => 'A', 'designation' => 'B', 'hero_title' => 'C', 'availability_enabled' => true, 'availability_text' => ''])
        ->assertSessionHasErrors('availability_text');
});

it('updates website settings', function () {
    $this->put('/admin/settings', [
        'site_title' => 'New title', 'contact_success_message' => 'Thanks!', 'contact_enabled' => true,
        'contact_notify_enabled' => true, 'contact_notify_email' => 'me@example.com', 'blog_enabled' => true, 'projects_show_filters' => false,
    ])->assertSessionHasNoErrors();

    expect(app(Settings::class)->get('contact_notify_email'))->toBe('me@example.com')
        ->and(app(Settings::class)->get('blog_enabled'))->toBeTrue();
});

it('requires a notification email when notifications are on', function () {
    $this->put('/admin/settings', ['site_title' => 'T', 'contact_success_message' => 'M', 'contact_notify_enabled' => true])
        ->assertSessionHasErrors('contact_notify_email');
});

it('edits but never creates or deletes fixed SEO pages and sections', function () {
    $seo = SeoSetting::query()->firstOrFail();

    $this->put("/admin/seo/{$seo->id}", ['title' => 'Updated', 'description' => 'D', 'noindex' => false])->assertSessionHasNoErrors();
    expect($seo->fresh()->title)->toBe('Updated');

    $this->get('/admin/seo/create')->assertForbidden();
    $this->delete("/admin/seo/{$seo->id}")->assertForbidden();
    $this->delete('/admin/sections/'.PageSection::query()->value('id'))->assertForbidden();
});

it('validates navigation URLs', function () {
    $this->post('/admin/navigation', ['label' => 'Bad', 'url' => 'javascript:alert(1)', 'location' => 'header'])->assertSessionHasErrors('url');
    $this->post('/admin/navigation', ['label' => 'Blog', 'url' => '/blog', 'location' => 'footer', 'is_visible' => true, 'sort_order' => 1])->assertSessionHasNoErrors();

    expect(NavigationItem::query()->where('label', 'Blog')->exists())->toBeTrue();
});

it('reads, marks and deletes contact messages', function () {
    $message = ContactMessage::query()->create(['name' => 'A', 'email' => 'a@example.com', 'subject' => 'Hi', 'message' => 'Hello there friend']);

    $this->get('/admin/messages?status=unread')->assertInertia(fn (Assert $page) => $page->has('items.data', 1)->where('unread', 1));
    $this->get("/admin/messages/{$message->id}")->assertOk();
    expect($message->fresh()->read_at)->not->toBeNull();

    $this->patch("/admin/messages/{$message->id}/read");
    expect($message->fresh()->read_at)->toBeNull();

    $this->delete("/admin/messages/{$message->id}")->assertRedirect('/admin/messages');
    expect(ContactMessage::query()->count())->toBe(0);
});

it('shows real counts on the dashboard', function () {
    ContactMessage::query()->create(['name' => 'A', 'email' => 'a@example.com', 'subject' => 'Hi', 'message' => 'Hello there friend']);

    $this->get('/admin')->assertInertia(fn (Assert $page) => $page
        ->component('admin/Dashboard')
        ->where('stats.projects', 4)
        ->where('stats.published_projects', 4)
        ->where('stats.experiences', 4)
        ->where('stats.unread_messages', 1)
        ->where('setup.0.done', false));
});

it('renders markdown previews safely', function () {
    $this->postJson('/admin/markdown/preview', ['markdown' => '**bold** <script>alert(1)</script> [x](javascript:alert(1))'])
        ->assertOk()
        ->assertJsonPath('html', fn (string $html) => str_contains($html, '<strong>bold</strong>') && ! str_contains($html, '<script') && ! str_contains($html, 'javascript:'));
});

it('keeps list filters after deleting from the list', function () {
    $tech = Technology::query()->create(['name' => 'Temp', 'slug' => 'temp']);

    $this->from('/admin/technologies?search=Temp')->delete("/admin/technologies/{$tech->id}")
        ->assertRedirect('/admin/technologies?search=Temp');

    $other = Technology::query()->create(['name' => 'Temp2', 'slug' => 'temp2']);
    $this->from("/admin/technologies/{$other->id}/edit")->delete("/admin/technologies/{$other->id}")
        ->assertRedirect('/admin/technologies');
});
