<?php

namespace App\Models;

use App\Models\Concerns\FlushesContentCache;
use App\Models\Concerns\Orderable;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * "Engineering approach" content blocks.
 */
#[Fillable(['title', 'icon', 'body', 'is_published', 'sort_order'])]
class Principle extends Model
{
    use FlushesContentCache, Orderable, Publishable;

    public const ICONS = [
        'layers' => 'Layers',
        'shield-check' => 'Shield',
        'gauge' => 'Gauge',
        'flask-conical' => 'Flask',
        'git-branch' => 'Git branch',
        'database' => 'Database',
        'code' => 'Code',
        'users' => 'People',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'sort_order' => 'integer'];
    }
}
