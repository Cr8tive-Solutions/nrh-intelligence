<?php

namespace App\Models;

use Database\Factories\CountryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    /** @use HasFactory<CountryFactory> */
    use HasFactory;

    protected $fillable = ['name', 'code', 'flag', 'region'];

    public function scopeTypes(): HasMany
    {
        return $this->hasMany(ScopeType::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }
}
