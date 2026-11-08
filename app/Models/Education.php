<?php

namespace App\Models;

use App\Models\Concerns\FlushesContentCache;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['institution', 'degree', 'subject', 'start_year', 'graduation_year', 'grade', 'description', 'is_published', 'sort_order'])]
class Education extends Model
{
    use FlushesContentCache, Publishable;

    protected $table = 'education';

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'start_year' => 'integer',
            'graduation_year' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('graduation_year');
    }
}
