<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Read-only model for the shared report_versions table.
 * The admin portal owns all writes — this app only reads + serves downloads.
 */
class ReportVersion extends Model
{
    protected $table = 'report_versions';

    /**
     * Defensively no fillable — client portal never writes to this table.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    protected $casts = [
        'generated_at' => 'datetime',
        'snapshot' => 'array',
        'version' => 'integer',
    ];

    public function screeningRequest(): BelongsTo
    {
        return $this->belongsTo(ScreeningRequest::class);
    }

    public function isSuperseded(): bool
    {
        return self::where('supersedes_id', $this->id)->exists();
    }
}
