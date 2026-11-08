<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $subject
 * @property string $message
 * @property Carbon|null $read_at
 */
#[Fillable(['name', 'email', 'subject', 'message', 'ip_address', 'user_agent', 'read_at'])]
class ContactMessage extends Model
{
    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }
}
