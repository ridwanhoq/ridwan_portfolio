<?php

namespace App\Models;

use App\Models\Concerns\FlushesContentCache;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string $body
 * @property string $status
 * @property Carbon|null $published_at
 * @property Carbon $updated_at
 * @property string|null $meta_description
 * @property Media|null $cover
 */
#[Fillable(['title', 'slug', 'excerpt', 'body', 'cover_media_id', 'status', 'published_at', 'meta_description'])]
class Post extends Model
{
    use FlushesContentCache;

    public const STATUSES = Project::STATUSES;

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /** @return BelongsTo<Media, $this> */
    public function cover(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id');
    }
}
