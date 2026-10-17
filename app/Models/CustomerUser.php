<?php

namespace App\Models;

use App\Traits\HasHashid;
use Database\Factories\CustomerUserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Traits\HasRoles;

class CustomerUser extends Authenticatable
{
    /** @use HasFactory<CustomerUserFactory> */
    use HasFactory;

    use HasHashid;
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
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'two_factor_secret' => 'encrypted',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Whether the user has a confirmed authenticator-app (TOTP) factor. Login
     * uses the app factor when true and falls back to an emailed code otherwise.
     */
    public function hasTwoFactorAppEnabled(): bool
    {
        return ! is_null($this->two_factor_secret) && ! is_null($this->two_factor_confirmed_at);
    }

    /**
     * Email the password-reset link. Overrides the CanResetPassword default
     * (which targets the non-existent `password.reset` route) to point at the
     * client portal's own reset page and to send via the portal's plain-text
     * Mail::raw convention, matching the 2FA-code and invitation emails.
     */
    public function sendPasswordResetNotification($token): void
    {
        $url = route('client.reset', ['token' => $token]).'?email='.urlencode($this->email);

        Mail::raw(
            "We received a request to reset the password for your NRH Intelligence portal account.\n\n".
            "Reset your password using the link below (it expires in 60 minutes):\n{$url}\n\n".
            "If you didn't request this, you can safely ignore this email — your password won't change.",
            function ($message) {
                $message->to($this->email)->subject('Reset your NRH Intelligence password');
            }
        );
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
