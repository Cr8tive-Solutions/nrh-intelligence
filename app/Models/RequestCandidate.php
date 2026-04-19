<?php

namespace App\Models;

use Database\Factories\RequestCandidateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RequestCandidate extends Model
{
    /** @use HasFactory<RequestCandidateFactory> */
    use HasFactory;

    protected $fillable = [
        'screening_request_id',
        'identity_type_id',
        'name',
        'identity_number',
        'mobile',
        'remarks',
        'status',
    ];

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
        return $this->belongsToMany(ScopeType::class, 'candidate_scope_type')
            ->withPivot('status');
    }
}
