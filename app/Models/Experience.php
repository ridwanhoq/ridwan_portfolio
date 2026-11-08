<?php

namespace App\Models;

use App\Models\Concerns\FlushesContentCache;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $company
 * @property string $title
 * @property Carbon $start_date
 * @property Carbon|null $end_date
 * @property bool $is_current
 * @property list<string>|null $responsibilities
 * @property list<string>|null $contributions
 */
#[Fillable([
    'company', 'company_url', 'title', 'employment_type', 'location', 'focus', 'start_date', 'end_date',
    'is_current', 'description', 'responsibilities', 'contributions', 'is_published', 'sort_order',
])]
class Experience extends Model
{
    use FlushesContentCache, Publishable;

    public const EMPLOYMENT_TYPES = [
        'full_time' => 'Full-time',
        'part_time' => 'Part-time',
        'contract' => 'Contract',
        'freelance' => 'Freelance',
        'internship' => 'Internship',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
            'is_published' => 'boolean',
            'responsibilities' => 'array',
            'contributions' => 'array',
            'sort_order' => 'integer',
        ];
    }

    /** @return BelongsToMany<Technology, $this> */
    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class)->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Most recent first: current roles, then by start date.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeChronological(Builder $query): Builder
    {
        return $query->orderByDesc('is_current')->orderByDesc('start_date')->orderBy('sort_order');
    }
}
