<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\PrincipleRequest;
use App\Models\Principle;

/** @extends ResourceController<Principle> */
class PrincipleController extends ResourceController
{
    protected string $model = Principle::class;

    protected string $resource = 'principles';

    protected string $label = 'Approach block';

    /** @var list<string> */
    protected array $searchable = ['title', 'body'];

    /** @var list<string> */
    protected array $sortable = ['title', 'sort_order', 'updated_at'];

    /** @var list<string> */
    protected array $toggleable = ['is_published'];

    protected function formRequest(): string
    {
        return PrincipleRequest::class;
    }

    protected function defaults(): array
    {
        return ['is_published' => true, 'sort_order' => 0, 'icon' => 'layers'];
    }

    protected function options(): array
    {
        return ['icons' => self::toOptions(Principle::ICONS)];
    }
}
