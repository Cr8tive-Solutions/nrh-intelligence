<?php

namespace App\Models;

use Database\Factories\IdentityTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IdentityType extends Model
{
    /** @use HasFactory<IdentityTypeFactory> */
    use HasFactory;

    protected $fillable = ['name'];

    public function candidates(): HasMany
    {
        return $this->hasMany(RequestCandidate::class);
    }
}
