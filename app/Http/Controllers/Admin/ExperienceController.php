<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ExperienceRequest;
use App\Models\Experience;
use App\Models\Technology;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/** @extends ResourceController<Experience> */
class ExperienceController extends ResourceController
{
    protected string $model = Experience::class;

    protected string $resource = 'experiences';

    protected string $label = 'Experience';

    /** @var list<string> */
    protected array $searchable = ['company', 'title', 'location'];

    /** @var list<string> */
    protected array $sortable = ['start_date', 'company', 'title', 'sort_order', 'updated_at'];

    protected string $defaultSort = 'start_date';

    /** @var 'asc'|'desc' */
    protected string $defaultDirection = 'desc';

    /** @var list<string> */
    protected array $toggleable = ['is_published'];

    protected function formRequest(): string
    {
        return ExperienceRequest::class;
    }

    protected function save(Model $model, array $data): Model
    {
        $model->fill(Arr::except($data, 'technology_ids'))->save();
        $model->technologies()->sync($data['technology_ids'] ?? []);

        return $model;
    }

    protected function row(Model $model): array
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
            'company' => $model->company,
            'period' => $model->start_date->format('M Y').' – '.($model->is_current ? 'Present' : ($model->end_date?->format('M Y') ?? '')),
            'employment_type' => Experience::EMPLOYMENT_TYPES[$model->employment_type] ?? $model->employment_type,
            'is_current' => $model->is_current,
            'is_published' => $model->is_published,
        ];
    }

    protected function formData(Model $model): array
    {
        $model->load('technologies:id');

        return [
            ...$model->only(['company', 'company_url', 'title', 'employment_type', 'location', 'focus', 'is_current', 'description', 'is_published', 'sort_order']),
            'start_date' => $model->start_date->format('Y-m'),
            'end_date' => $model->end_date?->format('Y-m'),
            'responsibilities' => $model->responsibilities ?? [],
            'contributions' => $model->contributions ?? [],
            'technology_ids' => $model->technologies->pluck('id')->all(),
        ];
    }

    protected function defaults(): array
    {
        return ['employment_type' => 'full_time', 'is_published' => true, 'is_current' => false, 'sort_order' => 0, 'responsibilities' => [], 'contributions' => [], 'technology_ids' => []];
    }

    protected function options(): array
    {
        return [
            'employment_types' => self::toOptions(Experience::EMPLOYMENT_TYPES),
            'technologies' => Technology::query()->ordered()->get(['id', 'name'])->map(fn ($t) => ['value' => $t->id, 'label' => $t->name])->all(),
        ];
    }
}
