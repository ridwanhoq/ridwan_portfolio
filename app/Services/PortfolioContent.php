<?php

namespace App\Services;

use App\Http\Resources\ExperienceResource;
use App\Http\Resources\MediaResource;
use App\Http\Resources\ProjectCardResource;
use App\Http\Resources\ProjectResource;
use App\Models\Certification;
use App\Models\Education;
use App\Models\Experience;
use App\Models\NavigationItem;
use App\Models\PageSection;
use App\Models\Principle;
use App\Models\Profile;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\SkillCategory;
use App\Models\SocialLink;
use App\Support\ContentCache;

/**
 * Read-side queries for the public website. Results are plain arrays so they
 * can be cached and handed to Inertia directly.
 */
class PortfolioContent
{
    public function __construct(private readonly Settings $settings) {}

    /** @return array<string, mixed> */
    public function site(): array
    {
        return ContentCache::remember('site', function () {
            $profile = Profile::query()->first();

            return [
                'name' => $profile->full_name ?? config('app.name'),
                'short_name' => $profile->short_name ?? $profile->full_name ?? config('app.name'),
                'designation' => $profile?->designation,
                'email' => $profile?->email,
                'location' => $profile?->location,
                'availability' => [
                    'enabled' => (bool) $profile?->availability_enabled,
                    'text' => $profile?->availability_text,
                ],
                'socials' => SocialLink::query()->published()->ordered()
                    ->get(['platform', 'label', 'url', 'show_in_hero'])->toArray(),
                'nav' => [
                    'header' => $this->navigation('header'),
                    'footer' => $this->navigation('footer'),
                ],
                'footer_note' => $this->settings->get('footer_note'),
                'blog_enabled' => (bool) $this->settings->get('blog_enabled'),
                'contact_enabled' => (bool) $this->settings->get('contact_enabled'),
            ];
        });
    }

    /** @return array<string, mixed> */
    public function profile(): array
    {
        return ContentCache::remember('profile', function (): array {
            $profile = Profile::current();

            return [
                'full_name' => $profile->full_name,
                'designation' => $profile->designation,
                'tagline' => $profile->tagline,
                'hero_title' => $profile->hero_title,
                'hero_subtitle' => $profile->hero_subtitle,
                'summary' => $profile->summary,
                'location' => $profile->location,
                'email' => $profile->email,
                'phone' => $profile->show_phone ? $profile->phone : null,
                'work_preference' => $profile->work_preference,
                'years_experience' => $profile->years_experience,
                'years_primary_stack' => $profile->years_primary_stack,
                'primary_stack_label' => $profile->primary_stack_label,
                'hero_technologies' => $profile->hero_technologies ?? [],
                'languages' => $profile->languages ?? [],
                'photo' => $profile->photo ? MediaResource::make($profile->photo)->resolve() : null,
                'about' => array_filter([
                    'intro' => Markdown::toHtml($profile->about_intro),
                    'philosophy' => Markdown::toHtml($profile->philosophy),
                    'career_overview' => Markdown::toHtml($profile->career_overview),
                    'specialization' => Markdown::toHtml($profile->specialization),
                    'interests' => Markdown::toHtml($profile->interests),
                    'collaboration' => Markdown::toHtml($profile->collaboration),
                ]),
            ];
        });
    }

    /**
     * Enabled homepage sections in display order, keyed by section key.
     *
     * @return array<string, array<string, mixed>>
     */
    public function sections(): array
    {
        return ContentCache::remember('sections', fn () => PageSection::query()
            ->where('is_enabled', true)->ordered()
            ->get(['key', 'eyebrow', 'heading', 'intro'])
            ->keyBy('key')->toArray());
    }

    /** @return array<int, array<string, mixed>> */
    public function experiences(): array
    {
        return ContentCache::remember('experiences', fn () => ExperienceResource::collection(
            Experience::query()->published()->chronological()->with('technologies')->get()
        )->resolve());
    }

    /** @return array<int, array<string, mixed>> */
    public function featuredProjects(int $limit = 6): array
    {
        return ContentCache::remember("projects.featured.{$limit}", fn () => ProjectCardResource::collection(
            Project::query()->published()->where('is_featured', true)->ordered()
                ->with(['category', 'technologies', 'thumbnail'])->limit($limit)->get()
        )->resolve());
    }

    /** @return array<int, array<string, mixed>> */
    public function projects(): array
    {
        return ContentCache::remember('projects.all', fn () => ProjectCardResource::collection(
            Project::query()->published()->orderByDesc('is_featured')->ordered()
                ->with(['category', 'technologies', 'thumbnail'])->get()
        )->resolve());
    }

    /**
     * Categories that have at least one published project.
     *
     * @return array<int, array{name: string, slug: string, count: int}>
     */
    public function projectCategories(): array
    {
        return ContentCache::remember('projects.categories', fn () => ProjectCategory::query()->ordered()
            ->withCount(['projects' => fn ($q) => $q->published()])
            ->get()
            ->filter(fn (ProjectCategory $c) => $c->projects_count > 0)
            ->map(fn (ProjectCategory $c) => ['name' => $c->name, 'slug' => $c->slug, 'count' => (int) $c->projects_count])
            ->values()->all());
    }

    public function project(string $slug): ?Project
    {
        return Project::query()->published()->where('slug', $slug)
            ->with(['category', 'technologies', 'thumbnail', 'images.media'])
            ->first();
    }

    /** @return array<string, mixed> */
    public function projectPayload(Project $project): array
    {
        return ContentCache::remember("project.{$project->id}", fn () => ProjectResource::make($project)->resolve());
    }

    /** @return array<int, array<string, mixed>> */
    public function skills(): array
    {
        return ContentCache::remember('skills', fn () => SkillCategory::query()->published()->ordered()
            ->with(['skills' => fn ($q) => $q->published()])
            ->get()
            ->filter(fn (SkillCategory $category) => $category->skills->isNotEmpty())
            ->map(fn (SkillCategory $category) => [
                'name' => $category->name,
                'description' => $category->description,
                'skills' => $category->skills->pluck('name')->all(),
            ])->values()->all());
    }

    /** @return array<int, array<string, mixed>> */
    public function principles(): array
    {
        return ContentCache::remember('principles', fn () => Principle::query()->published()->ordered()
            ->get()
            ->map(fn (Principle $p) => ['title' => $p->title, 'icon' => $p->icon, 'html' => Markdown::toHtml($p->body)])
            ->all());
    }

    /** @return array<int, array<string, mixed>> */
    public function education(): array
    {
        return ContentCache::remember('education', fn () => Education::query()->published()->ordered()
            ->get(['id', 'institution', 'degree', 'subject', 'start_year', 'graduation_year', 'grade', 'description'])
            ->toArray());
    }

    /** @return array<int, array<string, mixed>> */
    public function certifications(): array
    {
        return ContentCache::remember('certifications', fn () => Certification::query()->published()->ordered()
            ->get()
            ->map(fn (Certification $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'issuer' => $c->issuer,
                'type' => Certification::TYPES[$c->type] ?? 'Certification',
                'issued' => $c->issued_on?->format('Y'),
                'issued_on' => $c->issued_on?->format('M Y'),
                'expires_on' => $c->expires_on?->format('M Y'),
                'credential_id' => $c->credential_id,
                'credential_url' => $c->credential_url,
                'description' => $c->description,
            ])->all());
    }

    public function publishedProjectCount(): int
    {
        return ContentCache::remember('projects.count', fn () => Project::query()->published()->count());
    }

    /** @return array<int, array{label: string, url: string, new_tab: bool}> */
    private function navigation(string $location): array
    {
        return NavigationItem::query()
            ->where('location', $location)->where('is_visible', true)->ordered()
            ->get()
            ->map(fn (NavigationItem $item) => [
                'label' => $item->label,
                'url' => $item->url,
                'new_tab' => $item->open_in_new_tab,
            ])->all();
    }
}
