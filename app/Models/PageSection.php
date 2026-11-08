<?php

namespace App\Models;

use App\Models\Concerns\FlushesContentCache;
use App\Models\Concerns\Orderable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Homepage section: toggle, ordering and headings.
 *
 * @property string $key
 * @property bool $is_enabled
 */
#[Fillable(['key', 'label', 'eyebrow', 'heading', 'intro', 'is_enabled', 'sort_order'])]
class PageSection extends Model
{
    use FlushesContentCache, Orderable;

    protected function casts(): array
    {
        return ['is_enabled' => 'boolean', 'sort_order' => 'integer'];
    }
}
