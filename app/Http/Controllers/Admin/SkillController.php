<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SkillRequest;
use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Database\Eloquent\Model;

/** @extends ResourceController<Skill> */
class SkillController extends ResourceController
{
    protected string $model = Skill::class;

    protected string $resource = 'skills';

    protected string $label = 'Skill';

    /** @var list<string> */
    protected array $searchable = ['name'];

    /** @var list<string> */
    protected array $sortable = ['name', 'sort_order', 'skill_category_id', 'updated_at'];

    protected string $defaultSort = 'skill_category_id';

    /** @var list<string> */
    protected array $with = ['category'];

    /** @var list<string> */
    protected array $toggleable = ['is_published'];

    /** @var array<string, string> */
    protected array $filters = ['category' => 'skill_category_id'];

    protected int $perPage = 25;

    protected function formRequest(): string
    {
        return SkillRequest::class;
    }

    protected function row(Model $model): array
    {
        return [...$model->only(['id', 'name', 'is_published', 'sort_order']), 'category' => $model->category?->name];
    }

    protected function defaults(): array
    {
        return ['is_published' => true, 'sort_order' => 0, 'skill_category_id' => request()->integer('category') ?: null];
    }

    protected function options(): array
    {
        return [
            'categories' => SkillCategory::query()->ordered()->get(['id', 'name'])->map(fn ($c) => ['value' => $c->id, 'label' => $c->name])->all(),
        ];
    }
}
