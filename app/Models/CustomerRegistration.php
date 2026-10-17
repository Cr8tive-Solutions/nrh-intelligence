<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Invite-to-register onboarding record. The table and the staff side (invite,
 * review, approve) live in the admin portal; this app only shows the invitee
 * the registration form and records what they submit. Approval — which creates
 * the customer and the Owner login — happens in the admin portal.
 */
class CustomerRegistration extends Model
{
    protected $fillable = [
        'status', 'company_name', 'registration_no', 'address', 'country', 'industry',
        'contact_name', 'contact_designation', 'contact_phone', 'password_hash', 'submitted_at',
    ];

    protected $hidden = ['token_hash', 'password_hash'];

    protected $casts = [
        'expires_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    /** Only the SHA-256 of the emailed token is stored. */
    public static function findByToken(string $token): ?self
    {
        return self::where('token_hash', hash('sha256', $token))->first();
    }

    public function isOpen(): bool
    {
        return $this->status === 'invited' && $this->expires_at->isFuture();
    }
}
