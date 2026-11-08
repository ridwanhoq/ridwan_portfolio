<?php

namespace App\Models;

use App\Models\Concerns\FlushesContentCache;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $page_key
 * @property string $label
 * @property string|null $title
 * @property string|null $description
 * @property bool $noindex
 * @property Media|null $ogImage
 */
#[Fillable(['page_key', 'label', 'title', 'description', 'og_image_media_id', 'noindex'])]
class SeoSetting extends Model
{
    use FlushesContentCache;

    protected function casts(): array
    {
        return ['noindex' => 'boolean'];
    }

    /** @return BelongsTo<Media, $this> */
    public function ogImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'og_image_media_id');
    }
}
