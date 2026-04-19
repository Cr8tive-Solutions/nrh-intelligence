<?php

namespace App\Models;

use Database\Factories\ScreeningRequestFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScreeningRequest extends Model
{
    /** @use HasFactory<ScreeningRequestFactory> */
    use HasFactory;

    protected $fillable = ['customer_id', 'customer_user_id', 'reference', 'status'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(CustomerUser::class, 'customer_user_id');
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(RequestCandidate::class);
    }

    /** @return Builder<static> */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['new', 'in_progress', 'flagged']);
    }

    /** @return Builder<static> */
    public function scopeComplete(Builder $query): Builder
    {
        return $query->where('status', 'complete');
    }
}
