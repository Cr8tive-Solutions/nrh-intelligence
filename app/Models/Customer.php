<?php

namespace App\Models;

use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'registration_no',
        'address',
        'country',
        'industry',
        'contact_name',
        'contact_email',
        'contact_phone',
        'balance',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(CustomerUser::class);
    }

    public function agreement(): HasOne
    {
        return $this->hasOne(Agreement::class);
    }

    public function screeningRequests(): HasMany
    {
        return $this->hasMany(ScreeningRequest::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopePrices(): HasMany
    {
        return $this->hasMany(CustomerScopePrice::class);
    }
}
