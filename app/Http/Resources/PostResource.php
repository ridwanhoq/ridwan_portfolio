<?php

namespace App\Http\Resources;

use App\Models\Post;
use App\Services\Markdown;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Post */
class PostResource extends JsonResource
{
    public bool $withBody = false;

    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $text = Markdown::toText($this->body, 10_000);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt ?: Markdown::toText($this->body, 200),
            'published_at' => $this->published_at?->toIso8601String(),
            'published_label' => $this->published_at?->format('M j, Y'),
            'reading_minutes' => max(1, (int) ceil(str_word_count($text) / 220)),
            'cover' => $this->cover ? MediaResource::make($this->cover)->resolve() : null,
            'body_html' => $this->when($this->withBody, fn () => Markdown::toHtml($this->body)),
        ];
    }

    public function withBody(): self
    {
        $this->withBody = true;

        return $this;
    }
}
