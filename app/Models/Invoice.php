<?php

namespace App\Models;

use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'number',
        'period',
        'period_date',
        'period_end',
        'status',
        'issued_at',
        'due_at',
        'subtotal',
        'tax',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'due_at' => 'date',
            'period_date' => 'date',
            'period_end' => 'date',
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    /**
     * Next invoice number, INV-YYYY-NNNN. Mirrors the admin portal's generator
     * (both write the shared invoices table): a transaction-scoped advisory
     * lock serialises concurrent generators so the read-then-increment can't
     * race and trip the unique constraint on `number`. Call inside a
     * DB::transaction().
     */
    public static function generateNumber(): string
    {
        DB::select('SELECT pg_advisory_xact_lock(hashtext(?))', ['invoices.number']);

        $year = now()->year;
        // Order by numeric tail so mixed-width sequences sort correctly.
        $last = self::where('number', 'like', "INV-{$year}-%")
            ->orderByRaw('LENGTH(number) DESC, number DESC')
            ->first();
        $seq = $last ? ((int) substr(strrchr($last->number, '-'), 1)) + 1 : 1;

        return "INV-{$year}-".str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /** Requests billed on this invoice (screening_requests.invoice_id). */
    public function screeningRequests(): HasMany
    {
        return $this->hasMany(ScreeningRequest::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(InvoicePaymentReceipt::class)->latest();
    }

    /**
     * Sum of verified receipt amounts. Mirrors the admin portal's coverage
     * calculation (nrh-admin Invoice::verifiedReceiptsTotal) — admin flips
     * the invoice to 'paid' once this reaches the invoice total.
     */
    public function verifiedReceiptsTotal(): float
    {
        return (float) $this->receipts()
            ->where('status', 'verified')
            ->sum('amount_claimed');
    }

    /** Amount still owed after verified receipts. */
    public function outstandingTotal(): float
    {
        return max(0, round((float) $this->total - $this->verifiedReceiptsTotal(), 2));
    }
}
