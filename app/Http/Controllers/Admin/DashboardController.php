<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Experience;
use App\Models\Media;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;
use App\Models\SocialLink;
use App\Services\Settings;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Settings $settings): Response
    {
        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'projects' => Project::query()->count(),
                'published_projects' => Project::query()->published()->count(),
                'featured_projects' => Project::query()->published()->where('is_featured', true)->count(),
                'experiences' => Experience::query()->count(),
                'skills' => Skill::query()->count(),
                'media' => Media::query()->count(),
                'posts' => Post::query()->count(),
                'messages' => ContactMessage::query()->count(),
                'unread_messages' => ContactMessage::query()->unread()->count(),
            ],
            'recentMessages' => ContactMessage::query()->latest('id')->limit(5)->get()
                ->map(fn (ContactMessage $m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'subject' => $m->subject,
                    'is_read' => $m->read_at !== null,
                    'created_at' => $m->created_at?->toIso8601String(),
                ]),
            'recentProjects' => Project::query()->latest('updated_at')->limit(5)->get(['id', 'title', 'status', 'is_featured', 'updated_at'])
                ->map(fn (Project $p) => [
                    'id' => $p->id,
                    'title' => $p->title,
                    'status' => $p->status,
                    'is_featured' => $p->is_featured,
                    'updated_at' => $p->updated_at->toIso8601String(),
                ]),
            'checks' => [
                'resume' => $settings->get('resume_source') === 'upload' ? 'Uploaded PDF' : 'Generated from content',
                'notifications' => (bool) $settings->get('contact_notify_enabled'),
                'blog' => (bool) $settings->get('blog_enabled'),
                'mail_driver' => config('mail.default'),
                'placeholder_count' => $this->placeholderCount(),
            ],
            'setup' => $this->setupChecklist(),
        ]);
    }

    /**
     * Items that still need real content before they appear publicly.
     *
     * @return list<array{label: string, done: bool, href: string}>
     */
    private function setupChecklist(): array
    {
        $profile = Profile::query()->first();
        $social = fn (string $platform) => SocialLink::query()
            ->where('platform', $platform)->where('is_published', true)
            ->where('url', 'not like', '%PLACEHOLDER%')->exists();
        $withoutThumbnail = Project::query()->published()->whereNull('thumbnail_media_id')->count();

        return [
            ['label' => 'Add a public contact email to your profile', 'done' => filled($profile?->email), 'href' => route('admin.profile.edit')],
            ['label' => 'Add your LinkedIn URL and publish the link', 'done' => $social('linkedin'), 'href' => route('admin.social-links.index')],
            ['label' => 'Add your GitHub URL and publish the link', 'done' => $social('github'), 'href' => route('admin.social-links.index')],
            ['label' => 'Upload a professional profile photo (optional)', 'done' => $profile?->photo_media_id !== null, 'href' => route('admin.profile.edit')],
            ['label' => "Add thumbnails to {$withoutThumbnail} published project(s)", 'done' => $withoutThumbnail === 0, 'href' => route('admin.projects.index')],
        ];
    }

    /**
     * Number of content fields still containing "[PLACEHOLDER" markers from the seeders.
     */
    private function placeholderCount(): int
    {
        $count = 0;

        foreach ([Project::query(), Experience::query(), Profile::query()] as $query) {
            foreach ($query->get() as $model) {
                foreach ($model->getAttributes() as $value) {
                    if (is_string($value) && Str::contains($value, '[PLACEHOLDER')) {
                        $count++;
                    }
                }
            }
        }

        return $count;
    }
}
