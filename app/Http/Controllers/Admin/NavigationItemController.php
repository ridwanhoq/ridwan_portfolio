<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\NavigationItemRequest;
use App\Models\NavigationItem;

/** @extends ResourceController<NavigationItem> */
class NavigationItemController extends ResourceController
{
    protected string $model = NavigationItem::class;

    protected string $resource = 'navigation';

    protected string $label = 'Navigation item';

    /** @var list<string> */
    protected array $searchable = ['label', 'url'];

    /** @var list<string> */
    protected array $sortable = ['label', 'location', 'sort_order', 'updated_at'];

    /** @var list<string> */
    protected array $toggleable = ['is_visible'];

    /** @var array<string, string> */
    protected array $filters = ['location' => 'location'];

    protected function formRequest(): string
    {
        return NavigationItemRequest::class;
    }

    protected function defaults(): array
    {
        return ['location' => request()->query('location', 'header'), 'is_visible' => true, 'open_in_new_tab' => false, 'sort_order' => 0];
    }

    protected function options(): array
    {
        return ['locations' => self::toOptions(NavigationItem::LOCATIONS)];
    }
}
