<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePaymentReceipt extends Model
{
    protected $fillable = [
        'invoice_id', 'uploaded_by_customer_user_id',
        'file_path', 'file_name',
        'amount_claimed', 'paid_on', 'reference', 'notes',
        'status', 'verified_by_admin_id', 'verified_at', 'verification_note',
    ];

    protected $casts = [
        'amount_claimed' => 'decimal:2',
        'paid_on' => 'date',
        'verified_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
