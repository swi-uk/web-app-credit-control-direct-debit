<?php

namespace App\Domain\Submission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionItem extends Model
{
    protected $fillable = [
        'submission_batch_id',
        'entity_type',
        'entity_id',
        'status',
        'last_error',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(SubmissionBatch::class, 'submission_batch_id');
    }
}
