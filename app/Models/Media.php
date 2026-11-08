<?php

namespace App\Models;

use App\Models\Concerns\FlushesContentCache;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $disk
 * @property string $directory
 * @property string $filename
 * @property string $original_name
 * @property string $mime_type
 * @property int $size
 * @property int|null $width
 * @property int|null $height
 * @property array<string, string>|null $variants
 * @property string|null $alt
 * @property string|null $caption
 * @property-read string $url
 * @property-read string|null $webp_url
 * @property-read string $thumb_url
 * @property-read bool $is_image
 */
#[Fillable(['disk', 'directory', 'filename', 'original_name', 'mime_type', 'size', 'width', 'height', 'variants', 'alt', 'caption', 'uploaded_by'])]
class Media extends Model
{
    use FlushesContentCache;

    protected $appends = ['url', 'webp_url', 'thumb_url', 'is_image'];

    protected function casts(): array
    {
        return [
            'variants' => 'array',
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public function path(?string $file = null): string
    {
        return $this->directory.'/'.($file ?? $this->filename);
    }

    /** @return list<string> */
    public function allPaths(): array
    {
        return array_map(
            fn (string $file) => $this->path($file),
            [$this->filename, ...array_values($this->variants ?? [])],
        );
    }

    /** @return Attribute<string, never> */
    protected function url(): Attribute
    {
        return Attribute::get(fn () => Storage::disk($this->disk)->url($this->path()));
    }

    /** @return Attribute<string|null, never> */
    protected function webpUrl(): Attribute
    {
        return Attribute::get(fn () => $this->variantUrl('webp'));
    }

    /** @return Attribute<string, never> */
    protected function thumbUrl(): Attribute
    {
        return Attribute::get(fn () => $this->variantUrl('thumb') ?? $this->url);
    }

    /** @return Attribute<bool, never> */
    protected function isImage(): Attribute
    {
        return Attribute::get(fn () => str_starts_with($this->mime_type, 'image/'));
    }

    private function variantUrl(string $variant): ?string
    {
        $file = $this->variants[$variant] ?? null;

        return $file ? Storage::disk($this->disk)->url($this->path($file)) : null;
    }
}
