<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\PageSectionRequest;
use App\Models\PageSection;

/** @extends ResourceController<PageSection> */
class PageSectionController extends ResourceController
{
    protected string $model = PageSection::class;

    protected string $resource = 'sections';

    protected string $label = 'Section';

    /** @var list<string> */
    protected array $sortable = ['sort_order', 'label'];

    /** @var list<string> */
    protected array $toggleable = ['is_enabled'];

    protected function formRequest(): string
    {
        return PageSectionRequest::class;
    }
}
