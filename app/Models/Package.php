<?php

namespace App\Models;

use Database\Factories\PackageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Package extends Model
{
    /** @use HasFactory<PackageFactory> */
    use HasFactory;

    protected $fillable = ['customer_id', 'country_id', 'name'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function scopeTypes(): BelongsToMany
    {
        return $this->belongsToMany(ScopeType::class);
    }
}
