<?php

namespace App\Models;

use App\Models\Concerns\FlushesContentCache;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Single-row table holding the portfolio owner's public profile.
 *
 * @property int $id
 * @property string $full_name
 * @property string|null $short_name
 * @property string $designation
 * @property string|null $email
 * @property string|null $phone
 * @property bool $show_phone
 * @property bool $availability_enabled
 * @property list<string>|null $hero_technologies
 * @property list<string>|null $languages
 * @property int|null $photo_media_id
 * @property Media|null $photo
 */
#[Fillable([
    'full_name', 'short_name', 'designation', 'tagline', 'hero_title', 'hero_subtitle', 'summary',
    'location', 'email', 'phone', 'show_phone', 'work_preference', 'years_experience',
    'years_primary_stack', 'primary_stack_label', 'hero_technologies', 'availability_enabled',
    'availability_text', 'photo_media_id', 'about_intro', 'philosophy', 'career_overview',
    'specialization', 'interests', 'collaboration', 'languages',
])]
class Profile extends Model
{
    use FlushesContentCache;

    protected function casts(): array
    {
        return [
            'show_phone' => 'boolean',
            'availability_enabled' => 'boolean',
            'hero_technologies' => 'array',
            'languages' => 'array',
            'years_experience' => 'integer',
            'years_primary_stack' => 'integer',
        ];
    }

    /** @return BelongsTo<Media, $this> */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'photo_media_id');
    }

    public static function current(): self
    {
        return static::query()->with('photo')->firstOrFail();
    }
}
