<?php

namespace App\Models;

use App\Models\Concerns\FlushesContentCache;
use App\Models\Concerns\Orderable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $summary
 * @property string $status
 * @property bool $is_featured
 * @property Carbon|null $published_at
 * @property Carbon $updated_at
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property list<array{label: string, url: string}>|null $links
 * @property Media|null $thumbnail
 */
#[Fillable([
    'project_category_id', 'title', 'slug', 'client', 'project_type', 'period', 'summary', 'contribution',
    'thumbnail_media_id', 'live_url', 'repo_url', 'is_featured', 'status', 'published_at', 'sort_order',
    'overview', 'business_context', 'problem', 'challenges', 'role_details', 'architecture',
    'database_design', 'api_development', 'security', 'performance', 'testing_deployment', 'results',
    'links', 'meta_title', 'meta_description',
])]
class Project extends Model
{
    use FlushesContentCache, Orderable;

    public const STATUSES = ['draft' => 'Draft', 'published' => 'Published'];

    /**
     * Case-study sections in display order: column => heading.
     */
    public const CASE_STUDY_SECTIONS = [
        'overview' => 'Project overview',
        'business_context' => 'Business context',
        'problem' => 'Problem statement',
        'challenges' => 'Technical challenges',
        'role_details' => 'My role and responsibilities',
        'architecture' => 'Architecture decisions',
        'database_design' => 'Database design',
        'api_development' => 'API development',
        'security' => 'Security implementation',
        'performance' => 'Performance optimization',
        'testing_deployment' => 'Testing and deployment',
        'results' => 'Results and outcomes',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'links' => 'array',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(fn (Builder $q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' && ($this->published_at === null || $this->published_at->isPast());
    }

    /** @return BelongsTo<ProjectCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    /** @return BelongsTo<Media, $this> */
    public function thumbnail(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'thumbnail_media_id');
    }

    /** @return BelongsToMany<Technology, $this> */
    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class)->orderBy('sort_order')->orderBy('name');
    }

    /** @return HasMany<ProjectImage, $this> */
    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order')->orderBy('id');
    }
}
