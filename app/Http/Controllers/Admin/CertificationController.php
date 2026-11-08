<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CertificationRequest;
use App\Models\Certification;
use Illuminate\Database\Eloquent\Model;

/** @extends ResourceController<Certification> */
class CertificationController extends ResourceController
{
    protected string $model = Certification::class;

    protected string $resource = 'certifications';

    protected string $label = 'Certification';

    /** @var list<string> */
    protected array $searchable = ['name', 'issuer'];

    /** @var list<string> */
    protected array $sortable = ['name', 'issued_on', 'sort_order', 'updated_at'];

    /** @var list<string> */
    protected array $toggleable = ['is_published'];

    protected function formRequest(): string
    {
        return CertificationRequest::class;
    }

    protected function row(Model $model): array
    {
        return [
            ...$model->only(['id', 'name', 'issuer', 'is_published']),
            'type' => Certification::TYPES[$model->type] ?? $model->type,
            'issued_on' => $model->issued_on?->format('M Y'),
        ];
    }

    protected function formData(Model $model): array
    {
        return [
            ...$model->only(['name', 'issuer', 'type', 'credential_id', 'credential_url', 'description', 'is_published', 'sort_order']),
            'issued_on' => $model->issued_on?->format('Y-m'),
            'expires_on' => $model->expires_on?->format('Y-m'),
        ];
    }

    protected function defaults(): array
    {
        return ['type' => 'certification', 'is_published' => true, 'sort_order' => 0];
    }

    protected function options(): array
    {
        return ['types' => self::toOptions(Certification::TYPES)];
    }
}
