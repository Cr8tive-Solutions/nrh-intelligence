<?php

namespace App\Models;

use App\Traits\HasHashid;
use Database\Factories\ScreeningRequestFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ScreeningRequest extends Model
{
    use HasHashid;

    /** @use HasFactory<ScreeningRequestFactory> */
    use HasFactory;

    use LogsActivity;

    protected $fillable = ['customer_id', 'customer_user_id', 'reference', 'status', 'type', 'meta', 'rejection_reason', 'payment_slip_path', 'payment_slip_uploaded_at', 'payment_verified_at', 'payment_verified_by'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['reference', 'status', 'type'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('screening_request')
            ->setDescriptionForEvent(fn (string $event) => "Screening request {$event}");
    }

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'payment_slip_uploaded_at' => 'datetime',
            'payment_verified_at' => 'datetime',
        ];
    }

    public function hasPaymentSlip(): bool
    {
        return ! empty($this->payment_slip_path);
    }

    public function isPaymentVerified(): bool
    {
        return ! is_null($this->payment_verified_at);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(CustomerUser::class, 'customer_user_id');
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(RequestCandidate::class);
    }

    public function reportVersions(): HasMany
    {
        return $this->hasMany(ReportVersion::class);
    }

    /**
     * Total cash-payable amount = sum of scope prices across every candidate's
     * pivot rows. Customer-specific price overrides aren't reflected on
     * scope_types.price, so this is a best-effort estimate; admin's invoice
     * remains the source of truth.
     */
    public function cashTotal(): float
    {
        $candidateIds = $this->candidates()->pluck('id');
        if ($candidateIds->isEmpty()) {
            return 0.0;
        }

        $scopeIds = DB::table('candidate_scope_type')
            ->whereIn('request_candidate_id', $candidateIds)
            ->pluck('scope_type_id');

        if ($scopeIds->isEmpty()) {
            return 0.0;
        }

        $customerPrices = DB::table('customer_scope_prices')
            ->where('customer_id', $this->customer_id)
            ->whereIn('scope_type_id', $scopeIds)
            ->pluck('price', 'scope_type_id');

        $defaultPrices = DB::table('scope_types')
            ->whereIn('id', $scopeIds)
            ->pluck('price', 'id');

        $total = 0.0;
        foreach ($scopeIds as $sid) {
            $total += (float) ($customerPrices[$sid] ?? $defaultPrices[$sid] ?? 0);
        }

        return round($total, 2);
    }

    /**
     * Only the latest, non-superseded versions per type — the ones customers should see.
     */
    public function currentReportVersions(): HasMany
    {
        return $this->hasMany(ReportVersion::class)
            ->whereNotIn('id', fn ($q) => $q->select('supersedes_id')->from('report_versions')->whereNotNull('supersedes_id'))
            ->orderBy('type')
            ->orderByDesc('version');
    }

    /** @return Builder<static> */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['new', 'in_progress', 'complete', 'updated', 'rejected']);
    }

    /** @return Builder<static> */
    public function scopeComplete(Builder $query): Builder
    {
        return $query->whereIn('status', ['complete', 'updated']);
    }
}
