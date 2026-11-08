<?php

namespace App\Http\Controllers\Site;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\Markdown;
use Inertia\Response;

class BlogController extends SiteController
{
    public function index(): Response
    {
        $this->ensureEnabled();

        $posts = Post::query()->published()->with('cover')->latest('published_at')->paginate(10);

        return $this->page('site/blog/Index', [
            'posts' => PostResource::collection($posts->items())->resolve(),
            'pagination' => [
                'current' => $posts->currentPage(),
                'last' => $posts->lastPage(),
                'prev' => $posts->previousPageUrl(),
                'next' => $posts->nextPageUrl(),
            ],
        ], $this->seo->forPage('blog'));
    }

    public function show(string $slug): Response
    {
        $this->ensureEnabled();

        $post = Post::query()->published()->with('cover')->where('slug', $slug)->firstOrFail();

        return $this->page('site/blog/Show', [
            'post' => PostResource::make($post)->withBody()->resolve(),
        ], $this->seo->forPage('blog', [
            'title' => $post->title,
            'description' => $post->meta_description ?: ($post->excerpt ?: Markdown::toText($post->body)),
            'image' => $post->cover?->url,
            'type' => 'article',
            'json_ld' => [array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $post->title,
                'datePublished' => $post->published_at?->toIso8601String(),
                'dateModified' => $post->updated_at->toIso8601String(),
                'image' => $post->cover?->url,
                'author' => ['@type' => 'Person', 'name' => $this->content->site()['name'], 'url' => url('/')],
                'mainEntityOfPage' => route('blog.show', $post->slug),
            ])],
        ]));
    }

    private function ensureEnabled(): void
    {
        abort_unless((bool) $this->settings->get('blog_enabled'), 404);
    }
}
