<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\TechnologyRequest;
use App\Models\Technology;

/** @extends ResourceController<Technology> */
class TechnologyController extends ResourceController
{
    protected string $model = Technology::class;

    protected string $resource = 'technologies';

    protected string $label = 'Technology';

    /** @var list<string> */
    protected array $searchable = ['name', 'slug'];

    /** @var list<string> */
    protected array $sortable = ['name', 'sort_order', 'updated_at'];

    protected string $defaultSort = 'name';

    /** @var list<string> */
    protected array $withCount = ['projects', 'experiences'];

    protected int $perPage = 25;

    protected function formRequest(): string
    {
        return TechnologyRequest::class;
    }

    protected function defaults(): array
    {
        return ['sort_order' => 0];
    }
}
