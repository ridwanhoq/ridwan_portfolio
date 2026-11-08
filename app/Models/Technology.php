<?php

namespace App\Models;

use App\Models\Concerns\FlushesContentCache;
use App\Models\Concerns\Orderable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'slug', 'url', 'sort_order'])]
class Technology extends Model
{
    use FlushesContentCache, Orderable;

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    /** @return BelongsToMany<Project, $this> */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    /** @return BelongsToMany<Experience, $this> */
    public function experiences(): BelongsToMany
    {
        return $this->belongsToMany(Experience::class);
    }
}
