<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\PostRequest;
use App\Http\Resources\MediaResource;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;

/** @extends ResourceController<Post> */
class PostController extends ResourceController
{
    protected string $model = Post::class;

    protected string $resource = 'posts';

    protected string $label = 'Post';

    /** @var list<string> */
    protected array $searchable = ['title', 'excerpt'];

    /** @var list<string> */
    protected array $sortable = ['title', 'status', 'published_at', 'updated_at'];

    protected string $defaultSort = 'updated_at';

    /** @var 'asc'|'desc' */
    protected string $defaultDirection = 'desc';

    /** @var list<string> */
    protected array $with = ['cover'];

    /** @var array<string, string> */
    protected array $filters = ['status' => 'status'];

    protected function formRequest(): string
    {
        return PostRequest::class;
    }

    protected function save(Model $model, array $data): Model
    {
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = $model->published_at ?? now();
        }

        $model->fill($data)->save();

        return $model;
    }

    protected function row(Model $model): array
    {
        return [
            ...$model->only(['id', 'title', 'slug', 'status']),
            'published_at' => $model->published_at?->toIso8601String(),
            'updated_at' => $model->updated_at->toIso8601String(),
        ];
    }

    protected function formData(Model $model): array
    {
        return [
            ...$model->only(['title', 'slug', 'excerpt', 'body', 'cover_media_id', 'status', 'meta_description']),
            'published_at' => $model->published_at?->format('Y-m-d\TH:i'),
            'media' => $model->cover ? [$model->cover->id => MediaResource::make($model->cover)->resolve()] : [],
        ];
    }

    protected function defaults(): array
    {
        return ['status' => 'draft', 'media' => []];
    }

    protected function options(): array
    {
        return ['statuses' => self::toOptions(Post::STATUSES)];
    }
}
