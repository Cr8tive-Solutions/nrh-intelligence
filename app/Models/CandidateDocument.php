<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateDocument extends Model
{
    protected $fillable = [
        'request_candidate_id',
        'screening_request_id',
        'type',
        'file_path',
        'original_name',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(RequestCandidate::class, 'request_candidate_id');
    }

    public function screeningRequest(): BelongsTo
    {
        return $this->belongsTo(ScreeningRequest::class);
    }
}
