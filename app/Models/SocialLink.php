<?php

namespace App\Models;

use App\Models\Concerns\FlushesContentCache;
use App\Models\Concerns\Orderable;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['platform', 'label', 'url', 'show_in_hero', 'is_published', 'sort_order'])]
class SocialLink extends Model
{
    use FlushesContentCache, Orderable, Publishable;

    public const PLATFORMS = [
        'linkedin' => 'LinkedIn',
        'github' => 'GitHub',
        'gitlab' => 'GitLab',
        'x' => 'X (Twitter)',
        'stackoverflow' => 'Stack Overflow',
        'website' => 'Website',
        'email' => 'Email',
        'other' => 'Other',
    ];

    protected function casts(): array
    {
        return ['show_in_hero' => 'boolean', 'is_published' => 'boolean', 'sort_order' => 'integer'];
    }
}
