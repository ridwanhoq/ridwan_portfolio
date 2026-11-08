<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SocialLinkRequest;
use App\Models\SocialLink;

/** @extends ResourceController<SocialLink> */
class SocialLinkController extends ResourceController
{
    protected string $model = SocialLink::class;

    protected string $resource = 'social-links';

    protected string $label = 'Social link';

    /** @var list<string> */
    protected array $searchable = ['label', 'url', 'platform'];

    /** @var list<string> */
    protected array $sortable = ['label', 'platform', 'sort_order', 'updated_at'];

    /** @var list<string> */
    protected array $toggleable = ['is_published', 'show_in_hero'];

    protected function formRequest(): string
    {
        return SocialLinkRequest::class;
    }

    protected function defaults(): array
    {
        return ['platform' => 'linkedin', 'is_published' => true, 'show_in_hero' => true, 'sort_order' => 0];
    }

    protected function options(): array
    {
        return ['platforms' => self::toOptions(SocialLink::PLATFORMS)];
    }
}
