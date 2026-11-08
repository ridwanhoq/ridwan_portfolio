<?php

namespace App\Http\Resources;

use App\Models\Experience;
use App\Services\Markdown;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Experience */
class ExperienceResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $end = $this->is_current ? null : $this->end_date;

        return [
            'id' => $this->id,
            'company' => $this->company,
            'company_url' => $this->company_url,
            'title' => $this->title,
            'employment_type' => Experience::EMPLOYMENT_TYPES[$this->employment_type] ?? null,
            'location' => $this->location,
            'focus' => $this->focus,
            'is_current' => $this->is_current,
            'start' => $this->start_date->format('Y-m'),
            'end' => $end?->format('Y-m'),
            'period' => $this->start_date->format('M Y').' – '.($end ? $end->format('M Y') : 'Present'),
            'description_html' => Markdown::toHtml($this->description),
            'responsibilities' => $this->responsibilities ?? [],
            'contributions' => $this->contributions ?? [],
            'technologies' => $this->whenLoaded('technologies', fn () => $this->technologies->pluck('name')->all(), []),
        ];
    }
}
