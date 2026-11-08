<?php

namespace App\Http\Controllers\Site;

use App\Services\Markdown;
use Illuminate\Support\Str;
use Inertia\Response;

class ProjectController extends SiteController
{
    public function index(): Response
    {
        return $this->page('site/projects/Index', [
            'projects' => $this->content->projects(),
            'categories' => $this->content->projectCategories(),
            'showFilters' => (bool) $this->settings->get('projects_show_filters'),
        ], $this->seo->forPage('projects', [
            'json_ld' => [$this->seo->breadcrumbSchema([
                ['name' => 'Home', 'url' => url('/')],
                ['name' => 'Projects', 'url' => route('projects.index')],
            ])],
        ]));
    }

    public function show(string $slug): Response
    {
        $project = $this->content->project($slug);
        abort_if($project === null, 404);

        $payload = $this->content->projectPayload($project);
        $description = $project->meta_description ?: Str::limit($project->summary, 300);

        return $this->page('site/projects/Show', [
            'project' => $payload,
            'related' => collect($this->content->projects())
                ->where('slug', '!=', $project->slug)
                ->take(2)->values()->all(),
        ], $this->seo->forPage('projects', [
            'title' => $project->meta_title ?: "{$project->title} — Case study",
            'description' => $description ?: Markdown::toText($project->overview),
            'image' => $project->thumbnail?->url,
            'type' => 'article',
            'json_ld' => [
                array_filter([
                    '@context' => 'https://schema.org',
                    '@type' => 'CreativeWork',
                    'name' => $project->title,
                    'description' => $description,
                    'url' => route('projects.show', $project->slug),
                    'image' => $project->thumbnail?->url,
                    'dateModified' => $project->updated_at->toIso8601String(),
                    'keywords' => implode(', ', $payload['technologies']) ?: null,
                    'creator' => ['@type' => 'Person', 'name' => $this->content->site()['name'], 'url' => url('/')],
                ]),
                $this->seo->breadcrumbSchema([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => 'Projects', 'url' => route('projects.index')],
                    ['name' => $project->title, 'url' => route('projects.show', $project->slug)],
                ]),
            ],
        ]));
    }
}
