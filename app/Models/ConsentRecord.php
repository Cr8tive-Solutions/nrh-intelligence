<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsentRecord extends Model
{
    protected $fillable = [
        'request_candidate_id',
        'consented_at',
        'consent_version',
        'consent_text_snapshot',
        'evidence_type',
        'evidence_file_path',
        'captured_ip',
        'captured_user_agent',
        'captured_by_admin_id',
        'notes',
    ];

    protected $casts = [
        'consented_at' => 'datetime',
    ];

    public function requestCandidate(): BelongsTo
    {
        return $this->belongsTo(RequestCandidate::class);
    }
}
