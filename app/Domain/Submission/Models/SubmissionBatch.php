<?php

namespace App\Domain\Submission\Models;

use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubmissionBatch extends Model
{
    protected $fillable = [
        'merchant_site_id',
        'type',
        'status',
        'format_version',
        'file_path',
        'file_sha256',
        'record_count',
        'generated_at',
        'uploaded_at',
        'last_error',
        'external_ref',
    ];

    protected $casts = [
        'record_count' => 'integer',
        'generated_at' => 'datetime',
        'uploaded_at' => 'datetime',
    ];

    public function merchantSite(): BelongsTo
    {
        return $this->belongsTo(MerchantSite::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SubmissionItem::class);
    }
}
