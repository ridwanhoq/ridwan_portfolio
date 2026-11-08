<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ProjectCategoryRequest;
use App\Models\ProjectCategory;

/** @extends ResourceController<ProjectCategory> */
class ProjectCategoryController extends ResourceController
{
    protected string $model = ProjectCategory::class;

    protected string $resource = 'project-categories';

    protected string $label = 'Category';

    /** @var list<string> */
    protected array $searchable = ['name', 'slug'];

    /** @var list<string> */
    protected array $sortable = ['name', 'sort_order', 'updated_at'];

    /** @var list<string> */
    protected array $withCount = ['projects'];

    protected function formRequest(): string
    {
        return ProjectCategoryRequest::class;
    }

    protected function defaults(): array
    {
        return ['sort_order' => 0];
    }
}
