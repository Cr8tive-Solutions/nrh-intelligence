<?php

namespace App\Models;

use Database\Factories\CustomerUserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class CustomerUser extends Authenticatable
{
    /** @use HasFactory<CustomerUserFactory> */
    use HasFactory;

    use HasRoles;

    protected string $guard_name = 'customer_user';

    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'avatar',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function screeningRequests(): HasMany
    {
        return $this->hasMany(ScreeningRequest::class);
    }
}
