<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Project;
use App\Models\SeoSetting;
use App\Services\Settings;
use App\Support\ContentCache;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function sitemap(Settings $settings): Response
    {
        $xml = ContentCache::remember('sitemap', function () use ($settings) {
            $noindex = SeoSetting::query()->where('noindex', true)->pluck('page_key')->all();

            $pages = collect([
                'home' => [url('/'), '1.0'],
                'about' => [route('about'), '0.8'],
                'projects' => [route('projects.index'), '0.9'],
                'resume' => [route('resume'), '0.8'],
                'contact' => [route('contact'), '0.6'],
                'privacy' => [route('privacy'), '0.2'],
            ])->except($noindex)
                ->map(fn ($p): array => ['loc' => $p[0], 'priority' => $p[1], 'lastmod' => null])
                ->values()
                ->all();

            if (! in_array('projects', $noindex, true)) {
                foreach (Project::query()->published()->get(['slug', 'updated_at']) as $p) {
                    $pages[] = ['loc' => route('projects.show', $p->slug), 'priority' => '0.8', 'lastmod' => $p->updated_at->toAtomString()];
                }
            }

            if ($settings->get('blog_enabled') && ! in_array('blog', $noindex, true)) {
                $pages[] = ['loc' => route('blog.index'), 'priority' => '0.6', 'lastmod' => null];

                foreach (Post::query()->published()->get(['slug', 'updated_at']) as $p) {
                    $pages[] = ['loc' => route('blog.show', $p->slug), 'priority' => '0.6', 'lastmod' => $p->updated_at->toAtomString()];
                }
            }

            return view('sitemap', ['pages' => $pages])->render();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots(): Response
    {
        $lines = config('portfolio.allow_indexing')
            ? ['User-agent: *', 'Disallow: /admin', 'Disallow: /login', 'Disallow: /settings', '', 'Sitemap: '.route('sitemap')]
            : ['User-agent: *', 'Disallow: /'];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
