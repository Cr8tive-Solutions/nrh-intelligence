<?php

namespace App\Models;

use Database\Factories\RequestCandidateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class RequestCandidate extends Model
{
    /** @use HasFactory<RequestCandidateFactory> */
    use HasFactory;

    use LogsActivity;

    protected $fillable = [
        'screening_request_id',
        'identity_type_id',
        'name',
        'identity_number',
        'mobile',
        'remarks',
        'status',
    ];

    protected $casts = [
        'redacted_at' => 'datetime',
    ];

    public function isRedacted(): bool
    {
        return $this->redacted_at !== null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'status', 'remarks'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('candidate')
            ->setDescriptionForEvent(fn (string $event) => "Candidate {$event}");
    }

    public function screeningRequest(): BelongsTo
    {
        return $this->belongsTo(ScreeningRequest::class);
    }

    public function identityType(): BelongsTo
    {
        return $this->belongsTo(IdentityType::class);
    }

    public function scopeTypes(): BelongsToMany
    {
        return $this->belongsToMany(ScopeType::class, 'candidate_scope_type', 'request_candidate_id', 'scope_type_id')
            ->using(CandidateScopeType::class)
            ->withPivot('status', 'assigned_at', 'started_at', 'completed_at', 'findings');
    }

    public function consentRecords(): HasMany
    {
        return $this->hasMany(ConsentRecord::class);
    }
}
