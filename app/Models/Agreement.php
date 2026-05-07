<?php

namespace App\Models;

use Database\Factories\AgreementFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agreement extends Model
{
    /** @use HasFactory<AgreementFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'type',
        'start_date',
        'expiry_date',
        'sla_tat',
        'billing',
        'payment',
        'terms',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'expiry_date' => 'date',
            'terms' => 'array',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function isCashBilled(): bool
    {
        $value = strtolower(trim((string) $this->billing));
        $aliases = array_map('strtolower', (array) config('billing.cash_aliases', []));

        return in_array($value, $aliases, true);
    }

    public function isCreditBilled(): bool
    {
        return ! $this->isCashBilled();
    }

    public function getDaysLeftAttribute(): int
    {
        return (int) max(0, now()->diffInDays($this->expiry_date, false));
    }
}
