<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ProjectRequest;
use App\Http\Resources\MediaResource;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectImage;
use App\Models\Technology;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/** @extends ResourceController<Project> */
class ProjectController extends ResourceController
{
    protected string $model = Project::class;

    protected string $resource = 'projects';

    protected string $label = 'Project';

    /** @var list<string> */
    protected array $searchable = ['title', 'client', 'summary'];

    /** @var list<string> */
    protected array $sortable = ['title', 'sort_order', 'status', 'updated_at', 'published_at'];

    /** @var list<string> */
    protected array $with = ['category', 'thumbnail'];

    /** @var list<string> */
    protected array $toggleable = ['is_featured'];

    /** @var array<string, string> */
    protected array $filters = ['status' => 'status', 'category' => 'project_category_id'];

    protected function formRequest(): string
    {
        return ProjectRequest::class;
    }

    /**
     * Saves the project, its technologies and its gallery together.
     */
    protected function save(Model $model, array $data): Model
    {
        if ($data['status'] === 'published' && empty($data['published_at']) && ! $model->published_at) {
            $data['published_at'] = now();
        }

        $model->fill(Arr::except($data, ['technology_ids', 'images']))->save();
        $model->technologies()->sync($data['technology_ids'] ?? []);

        $model->images()->delete();

        foreach (array_values($data['images'] ?? []) as $position => $image) {
            $model->images()->create([...$image, 'sort_order' => $position]);
        }

        return $model;
    }

    protected function row(Model $model): array
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
            'slug' => $model->slug,
            'category' => $model->category?->name,
            'status' => $model->status,
            'is_featured' => $model->is_featured,
            'thumbnail' => $model->thumbnail?->thumb_url,
            'updated_at' => $model->updated_at->toIso8601String(),
            'public_url' => $model->isPublished() ? route('projects.show', $model->slug) : null,
        ];
    }

    protected function formData(Model $model): array
    {
        $model->load(['technologies:id', 'images.media']);

        return [
            ...Arr::except($model->attributesToArray(), ['id', 'created_at', 'updated_at']),
            'published_at' => $model->published_at?->format('Y-m-d\TH:i'),
            'links' => $model->links ?? [],
            'technology_ids' => $model->technologies->pluck('id')->all(),
            'images' => $model->images->map(fn (ProjectImage $image) => [
                'media_id' => $image->media_id,
                'caption' => $image->caption,
                'kind' => $image->kind,
            ])->all(),
            'media' => collect([$model->thumbnail, ...$model->images->pluck('media')])
                ->filter()->unique('id')
                ->mapWithKeys(fn ($m) => [$m->id => MediaResource::make($m)->resolve()])->all(),
            'public_url' => $model->isPublished() ? route('projects.show', $model->slug) : null,
        ];
    }

    protected function defaults(): array
    {
        return ['status' => 'draft', 'is_featured' => false, 'sort_order' => 0, 'links' => [], 'images' => [], 'technology_ids' => [], 'media' => []];
    }

    protected function options(): array
    {
        return [
            'categories' => ProjectCategory::query()->ordered()->get(['id', 'name'])->map(fn ($c) => ['value' => $c->id, 'label' => $c->name])->all(),
            'technologies' => Technology::query()->ordered()->get(['id', 'name'])->map(fn ($t) => ['value' => $t->id, 'label' => $t->name])->all(),
            'statuses' => self::toOptions(Project::STATUSES),
            'image_kinds' => self::toOptions(ProjectImage::KINDS),
            'case_study_sections' => self::toOptions(Project::CASE_STUDY_SECTIONS),
        ];
    }
}
