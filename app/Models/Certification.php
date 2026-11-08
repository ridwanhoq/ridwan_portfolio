<?php

namespace App\Models;

use App\Models\Concerns\FlushesContentCache;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $issuer
 * @property string $type
 * @property Carbon|null $issued_on
 * @property Carbon|null $expires_on
 */
#[Fillable(['name', 'issuer', 'type', 'issued_on', 'expires_on', 'credential_id', 'credential_url', 'description', 'is_published', 'sort_order'])]
class Certification extends Model
{
    use FlushesContentCache, Publishable;

    public const TYPES = ['certification' => 'Certification', 'training' => 'Training', 'award' => 'Award'];

    protected function casts(): array
    {
        return [
            'issued_on' => 'date',
            'expires_on' => 'date',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('issued_on');
    }
}
