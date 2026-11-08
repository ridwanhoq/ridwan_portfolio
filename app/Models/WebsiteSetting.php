<?php

namespace App\Models;

use App\Models\Concerns\FlushesContentCache;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $key
 * @property mixed $value
 */
#[Fillable(['key', 'value'])]
class WebsiteSetting extends Model
{
    use FlushesContentCache;

    protected function casts(): array
    {
        return ['value' => 'json'];
    }
}
