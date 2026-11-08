<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Shared CRUD behaviour for admin modules: search, filters, sorting,
 * pagination, validation via a Form Request, policies, transactions,
 * toasts and quick status toggles.
 *
 * Subclasses only declare their model, request and any custom mapping.
 *
 * @template TModel of Model
 */
abstract class ResourceController extends Controller
{
    use AuthorizesRequests;

    /** @var class-string<TModel> */
    protected string $model;

    /** Route prefix and frontend config key, e.g. "experiences". */
    protected string $resource;

    /** Singular label used in toasts. */
    protected string $label;

    /** @var list<string> */
    protected array $searchable = [];

    /** @var list<string> */
    protected array $sortable = ['sort_order', 'created_at', 'updated_at'];

    protected string $defaultSort = 'sort_order';

    /** @var 'asc'|'desc' */
    protected string $defaultDirection = 'asc';

    /** @var list<string> */
    protected array $with = [];

    /** @var list<string> */
    protected array $withCount = [];

    /**
     * Boolean columns that can be flipped from the index table.
     *
     * @var list<string>
     */
    protected array $toggleable = [];

    /**
     * Query string filter => column.
     *
     * @var array<string, string>
     */
    protected array $filters = [];

    protected int $perPage = 15;

    /** @return class-string<AdminRequest> */
    abstract protected function formRequest(): string;

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', $this->model);

        $query = $this->model::query()->with($this->with)->withCount($this->withCount);
        $search = trim((string) $request->query('search', ''));

        if ($search !== '' && $this->searchable !== []) {
            $term = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function (Builder $q) use ($term) {
                foreach ($this->searchable as $column) {
                    $q->orWhere($column, 'like', $term);
                }
            });
        }

        $activeFilters = [];

        foreach ($this->filters as $param => $column) {
            $value = $request->query($param);

            if (is_string($value) && $value !== '') {
                $query->where($column, $value);
                $activeFilters[$param] = $value;
            }
        }

        $sort = in_array($request->query('sort'), $this->sortable, true) ? (string) $request->query('sort') : $this->defaultSort;
        $direction = match ($request->query('direction')) {
            'asc' => 'asc',
            'desc' => 'desc',
            default => $this->defaultDirection,
        };

        $items = $query->orderBy($sort, $direction)->orderByDesc('id')
            ->paginate($this->perPage)
            ->withQueryString()
            ->through(fn (Model $model) => $this->row($model));

        return Inertia::render('admin/resource/Index', [
            'resource' => $this->resource,
            'items' => $items,
            'query' => ['search' => $search, 'sort' => $sort, 'direction' => $direction, ...$activeFilters],
            'options' => $this->options(),
            'abilities' => $this->abilities(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', $this->model);

        return $this->form(null);
    }

    public function store(): RedirectResponse
    {
        $this->authorize('create', $this->model);

        $data = $this->validated();
        DB::transaction(fn () => $this->save(new $this->model, $data));

        Inertia::flash('toast', ['type' => 'success', 'message' => "{$this->label} created."]);

        return to_route("admin.{$this->resource}.index");
    }

    public function edit(int $id): Response
    {
        $model = $this->find($id);
        $this->authorize('update', $model);

        return $this->form($model);
    }

    public function update(int $id): RedirectResponse
    {
        $model = $this->find($id);
        $this->authorize('update', $model);

        $data = $this->validated();
        DB::transaction(fn () => $this->save($model, $data));

        Inertia::flash('toast', ['type' => 'success', 'message' => "{$this->label} saved."]);

        return to_route("admin.{$this->resource}.edit", $model->getKey());
    }

    public function destroy(int $id): RedirectResponse
    {
        $model = $this->find($id);
        $this->authorize('delete', $model);

        DB::transaction(fn () => $this->remove($model));

        Inertia::flash('toast', ['type' => 'success', 'message' => "{$this->label} deleted."]);

        // Deleting from the list keeps its search, filters and page; deleting from the edit form returns to the list.
        $previous = url()->previous();
        $index = route("admin.{$this->resource}.index");

        return str_starts_with($previous, $index) && ! str_contains($previous, '/edit')
            ? redirect()->to($previous)
            : redirect()->to($index);
    }

    public function toggle(Request $request, int $id): RedirectResponse
    {
        $model = $this->find($id);
        $this->authorize('update', $model);

        $field = $request->validate(['field' => ['required', 'string', Rule::in($this->toggleable)]])['field'];

        $model->forceFill([$field => ! $model->getAttribute($field)])->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => "{$this->label} updated."]);

        return back();
    }

    /** @return TModel */
    protected function find(int $id): Model
    {
        return $this->model::query()->with($this->with)->findOrFail($id);
    }

    /**
     * @param  TModel|null  $model
     */
    protected function form(?Model $model): Response
    {
        return Inertia::render('admin/resource/Form', [
            'resource' => $this->resource,
            'item' => $model ? $this->formData($model) : $this->defaults(),
            'itemId' => $model?->getKey(),
            'options' => $this->options(),
            'abilities' => $this->abilities(),
        ]);
    }

    /** @return array<string, mixed> */
    protected function validated(): array
    {
        /** @var AdminRequest $request */
        $request = app($this->formRequest());

        return $request->validated();
    }

    /**
     * Persist validated data. Runs inside a transaction.
     *
     * @param  TModel  $model
     * @param  array<string, mixed>  $data
     * @return TModel
     */
    protected function save(Model $model, array $data): Model
    {
        $model->fill($data)->save();

        return $model;
    }

    /** @param TModel $model */
    protected function remove(Model $model): void
    {
        $model->delete();
    }

    /**
     * Row shape for the index table.
     *
     * @param  TModel  $model
     * @return array<string, mixed>
     */
    protected function row(Model $model): array
    {
        return $model->toArray();
    }

    /**
     * Values loaded into the edit form.
     *
     * @param  TModel  $model
     * @return array<string, mixed>
     */
    protected function formData(Model $model): array
    {
        return $model->toArray();
    }

    /**
     * Initial values for the create form.
     *
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [];
    }

    /**
     * Select options and other data the form needs.
     *
     * @return array<string, mixed>
     */
    protected function options(): array
    {
        return [];
    }

    /** @return array{create: bool, delete: bool} */
    protected function abilities(): array
    {
        $user = request()->user();

        return [
            'create' => (bool) $user?->can('create', $this->model),
            'delete' => (bool) $user?->can('delete', new $this->model),
        ];
    }

    /**
     * @param  array<string|int, string>  $map
     * @return list<array{value: string|int, label: string}>
     */
    protected static function toOptions(array $map): array
    {
        return array_values(collect($map)->map(fn ($label, $value) => ['value' => $value, 'label' => $label])->all());
    }
}
