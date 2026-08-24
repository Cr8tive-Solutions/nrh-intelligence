<?php

namespace App\Models;

use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    use LogsActivity;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'registration_no', 'address', 'country', 'industry', 'contact_name', 'contact_email', 'contact_phone'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('account')
            ->setDescriptionForEvent(fn (string $event) => "Account {$event}");
    }

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

    public function agreements(): HasMany
    {
        return $this->hasMany(Agreement::class);
    }

    private ?Agreement $currentAgreementMemo = null;

    private bool $currentAgreementResolved = false;

    /**
     * The agreement that governs billing right now: the unexpired agreement
     * with the latest expiry date, falling back to the most recently expired
     * one so a lapsed customer keeps their billing mode instead of silently
     * flipping to credit. Same rule as the admin portal's
     * Customer::activeAgreement() — keep the two in sync, or cash requests
     * get stranded (client accepts a payment slip the admin refuses to verify).
     */
    public function currentAgreement(): ?Agreement
    {
        if (! $this->currentAgreementResolved) {
            $agreements = $this->relationLoaded('agreements') ? $this->agreements : $this->agreements()->get();

            $this->currentAgreementMemo = $agreements
                ->filter(fn ($a) => $a->expiry_date && $a->expiry_date->isFuture())
                ->sortByDesc('expiry_date')
                ->first()
                ?? $agreements->sortByDesc('expiry_date')->first();
            $this->currentAgreementResolved = true;
        }

        return $this->currentAgreementMemo;
    }

    /**
     * @return 'cash'|'credit' — defaults to credit when no agreement is on file.
     */
    public function paymentMode(): string
    {
        return $this->currentAgreement()?->isCashBilled() ? 'cash' : 'credit';
    }

    public function isCashBilled(): bool
    {
        return $this->paymentMode() === 'cash';
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
