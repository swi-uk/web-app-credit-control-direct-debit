<?php

namespace App\Domain\Bacs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BacsReportItem extends Model
{
    protected $fillable = [
        'bacs_report_id',
        'record_type',
        'reference',
        'external_order_id',
        'amount',
        'code',
        'description',
        'raw_json',
        'matched_entity_type',
        'matched_entity_id',
        'item_hash',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'raw_json' => 'array',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(BacsReport::class, 'bacs_report_id');
    }
}
