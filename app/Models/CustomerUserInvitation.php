<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerUserInvitation extends Model
{
    protected $table = 'customer_user_invitations';

    protected $fillable = [
        'customer_user_id',
        'token',
        'expires_at',
        'accepted_at',
        'sent_count',
        'last_sent_at',
    ];

    protected $hidden = ['token'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
            'last_sent_at' => 'datetime',
        ];
    }

    /**
     * A new raw invitation token. Only its hash is stored (see hashToken());
     * the raw value goes into the emailed link and nowhere else.
     */
    public static function newRawToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * The value stored in the `token` column for a raw token: lowercase hex
     * SHA-256. Shared contract with the admin portal, which hashes the same way.
     */
    public static function hashToken(string $rawToken): string
    {
        return hash('sha256', $rawToken);
    }

    /**
     * Look up an invitation by the raw token from an invitation link.
     */
    public static function findByRawToken(string $rawToken): ?self
    {
        return static::with('customerUser.customer')
            ->where('token', static::hashToken($rawToken))
            ->first();
    }

    /**
     * The acceptance link for a raw token. Built at send time, because the
     * raw token cannot be recovered from the stored hash.
     */
    public static function acceptUrl(string $rawToken): string
    {
        return route('client.invitation.show', $rawToken);
    }

    public function customerUser(): BelongsTo
    {
        return $this->belongsTo(CustomerUser::class, 'customer_user_id');
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->accepted_at === null && $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isPending(): bool
    {
        return ! $this->isAccepted() && ! $this->isExpired();
    }
}
