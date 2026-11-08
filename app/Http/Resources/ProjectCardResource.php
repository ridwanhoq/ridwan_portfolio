<?php

namespace App\Http\Resources;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Project */
class ProjectCardResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'client' => $this->client,
            'project_type' => $this->project_type,
            'period' => $this->period,
            'contribution' => $this->contribution,
            'is_featured' => $this->is_featured,
            'live_url' => $this->live_url,
            'repo_url' => $this->repo_url,
            'category' => $this->whenLoaded('category', fn () => $this->category ? [
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ] : null),
            'technologies' => $this->whenLoaded('technologies', fn () => $this->technologies->pluck('name')->all(), []),
            'thumbnail' => $this->whenLoaded('thumbnail', fn () => $this->thumbnail ? MediaResource::make($this->thumbnail)->resolve() : null),
        ];
    }
}
