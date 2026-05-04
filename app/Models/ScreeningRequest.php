<?php

namespace App\Models;

use Database\Factories\ScreeningRequestFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ScreeningRequest extends Model
{
    /** @use HasFactory<ScreeningRequestFactory> */
    use HasFactory;

    use LogsActivity;

    protected $fillable = ['customer_id', 'customer_user_id', 'reference', 'status', 'type', 'meta'];

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
        return ['meta' => 'array'];
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
        return $query->whereIn('status', ['new', 'in_progress', 'flagged']);
    }

    /** @return Builder<static> */
    public function scopeComplete(Builder $query): Builder
    {
        return $query->where('status', 'complete');
    }
}
