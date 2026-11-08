<?php

namespace App\Http\Resources;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Media */
class MediaResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'webp_url' => $this->webp_url,
            'thumb_url' => $this->thumb_url,
            'width' => $this->width,
            'height' => $this->height,
            'alt' => $this->alt,
        ];
    }
}
