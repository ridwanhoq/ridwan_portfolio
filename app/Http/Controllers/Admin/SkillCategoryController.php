<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SkillCategoryRequest;
use App\Models\SkillCategory;

/** @extends ResourceController<SkillCategory> */
class SkillCategoryController extends ResourceController
{
    protected string $model = SkillCategory::class;

    protected string $resource = 'skill-categories';

    protected string $label = 'Skill category';

    /** @var list<string> */
    protected array $searchable = ['name'];

    /** @var list<string> */
    protected array $sortable = ['name', 'sort_order', 'updated_at'];

    /** @var list<string> */
    protected array $withCount = ['skills'];

    /** @var list<string> */
    protected array $toggleable = ['is_published'];

    protected function formRequest(): string
    {
        return SkillCategoryRequest::class;
    }

    protected function defaults(): array
    {
        return ['is_published' => true, 'sort_order' => 0];
    }
}
