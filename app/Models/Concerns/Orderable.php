<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait Orderable
{
    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
