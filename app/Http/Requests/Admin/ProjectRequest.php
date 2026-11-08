<?php

namespace App\Http\Requests\Admin;

use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Validation\Rule;

class ProjectRequest extends AdminRequest
{
    protected function prepareForValidation(): void
    {
        $this->slugFrom('title');

        $this->merge([
            'links' => array_values(array_filter((array) $this->input('links', []), fn ($l) => is_array($l) && trim((string) ($l['url'] ?? '')) !== '')),
            'images' => array_values(array_filter((array) $this->input('images', []), fn ($i) => is_array($i) && ! empty($i['media_id']))),
        ]);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $markdown = ['nullable', 'string', 'max:20000'];

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required_with:title', 'string', 'max:255', 'alpha_dash', Rule::unique('projects', 'slug')->ignore($this->routeId())],
            'project_category_id' => ['nullable', 'integer', 'exists:project_categories,id'],
            'client' => ['nullable', 'string', 'max:255'],
            'project_type' => ['nullable', 'string', 'max:255'],
            'period' => ['nullable', 'string', 'max:60'],
            'summary' => ['required', 'string', 'max:500'],
            'contribution' => ['nullable', 'string', 'max:1000'],
            'thumbnail_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'live_url' => ['nullable', 'string', 'max:2048', self::SAFE_URL],
            'repo_url' => ['nullable', 'string', 'max:2048', self::SAFE_URL],
            'is_featured' => ['boolean'],
            'status' => ['required', Rule::in(array_keys(Project::STATUSES))],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['integer', 'min:0', 'max:65535'],
            ...array_fill_keys(array_keys(Project::CASE_STUDY_SECTIONS), $markdown),
            'links' => ['array', 'max:10'],
            'links.*.label' => ['nullable', 'string', 'max:80'],
            'links.*.url' => ['required', 'string', 'max:2048', self::SAFE_URL],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'technology_ids' => ['array'],
            'technology_ids.*' => ['integer', 'exists:technologies,id'],
            'images' => ['array', 'max:30'],
            'images.*.media_id' => ['required', 'integer', 'exists:media,id'],
            'images.*.caption' => ['nullable', 'string', 'max:255'],
            'images.*.kind' => ['required', Rule::in(array_keys(ProjectImage::KINDS))],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['links.*.url' => 'link URL', 'images.*.media_id' => 'image', 'project_category_id' => 'category'];
    }
}
