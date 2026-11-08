<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\EducationRequest;
use App\Models\Education;

/** @extends ResourceController<Education> */
class EducationController extends ResourceController
{
    protected string $model = Education::class;

    protected string $resource = 'education';

    protected string $label = 'Education record';

    /** @var list<string> */
    protected array $searchable = ['institution', 'degree', 'subject'];

    /** @var list<string> */
    protected array $sortable = ['institution', 'graduation_year', 'sort_order', 'updated_at'];

    /** @var list<string> */
    protected array $toggleable = ['is_published'];

    protected function formRequest(): string
    {
        return EducationRequest::class;
    }

    protected function defaults(): array
    {
        return ['is_published' => true, 'sort_order' => 0];
    }
}
