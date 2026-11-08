<?php

namespace App\Models;

use App\Models\Concerns\FlushesContentCache;
use App\Models\Concerns\Orderable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['label', 'url', 'location', 'open_in_new_tab', 'is_visible', 'sort_order'])]
class NavigationItem extends Model
{
    use FlushesContentCache, Orderable;

    public const LOCATIONS = ['header' => 'Header', 'footer' => 'Footer'];

    protected function casts(): array
    {
        return ['open_in_new_tab' => 'boolean', 'is_visible' => 'boolean', 'sort_order' => 'integer'];
    }
}
