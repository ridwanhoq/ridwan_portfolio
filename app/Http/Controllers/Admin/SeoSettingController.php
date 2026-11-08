<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SeoSettingRequest;
use App\Http\Resources\MediaResource;
use App\Models\SeoSetting;
use Illuminate\Database\Eloquent\Model;

/** @extends ResourceController<SeoSetting> */
class SeoSettingController extends ResourceController
{
    protected string $model = SeoSetting::class;

    protected string $resource = 'seo';

    protected string $label = 'SEO settings';

    /** @var list<string> */
    protected array $searchable = ['label', 'title'];

    /** @var list<string> */
    protected array $sortable = ['label', 'updated_at', 'id'];

    protected string $defaultSort = 'id';

    /** @var list<string> */
    protected array $with = ['ogImage'];

    /** @var list<string> */
    protected array $toggleable = ['noindex'];

    protected function formRequest(): string
    {
        return SeoSettingRequest::class;
    }

    protected function row(Model $model): array
    {
        return [
            ...$model->only(['id', 'label', 'page_key', 'title', 'noindex']),
            'description_length' => mb_strlen((string) $model->description),
        ];
    }

    protected function formData(Model $model): array
    {
        return [
            ...$model->only(['label', 'page_key', 'title', 'description', 'og_image_media_id', 'noindex']),
            'media' => $model->ogImage ? [$model->ogImage->id => MediaResource::make($model->ogImage)->resolve()] : [],
        ];
    }
}
