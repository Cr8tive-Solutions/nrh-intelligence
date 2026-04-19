<?php

namespace App\Models;

use Database\Factories\ScopeTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ScopeType extends Model
{
    /** @use HasFactory<ScopeTypeFactory> */
    use HasFactory;

    protected $fillable = ['country_id', 'name', 'turnaround', 'price', 'description'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class);
    }

    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(RequestCandidate::class, 'candidate_scope_type')
            ->withPivot('status');
    }
}
