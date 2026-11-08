<?php

namespace App\Http\Resources;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Services\Markdown;
use Illuminate\Http\Request;

/**
 * Full case study payload. Only sections with content are included.
 *
 * @mixin Project
 */
class ProjectResource extends ProjectCardResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $sections = [];

        foreach (Project::CASE_STUDY_SECTIONS as $column => $heading) {
            $html = Markdown::toHtml($this->{$column});

            if ($html !== null) {
                $sections[] = ['key' => $column, 'title' => $heading, 'html' => $html];
            }
        }

        return [
            ...parent::toArray($request),
            'sections' => $sections,
            'images' => $this->whenLoaded('images', fn () => $this->images
                ->filter(fn (ProjectImage $image) => $image->media !== null)
                ->map(fn (ProjectImage $image) => [
                    'id' => $image->id,
                    'kind' => $image->kind,
                    'caption' => $image->caption,
                    'media' => MediaResource::make($image->media)->resolve(),
                ])->values()->all(), []),
            'links' => collect($this->links ?? [])
                ->filter(fn ($link) => ! empty($link['url']))
                ->map(fn ($link) => ['label' => $link['label'] ?: $link['url'], 'url' => $link['url']])
                ->values()->all(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
