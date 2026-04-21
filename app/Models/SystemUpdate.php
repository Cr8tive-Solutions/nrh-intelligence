<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SystemUpdate extends Model
{
    protected $fillable = [
        'version',
        'title',
        'body',
        'type',
        'is_published',
        'released_at',
    ];

    protected function casts(): array
    {
        return [
            'released_at'  => 'date',
            'is_published' => 'boolean',
        ];
    }

    /** @return Builder<static> */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
