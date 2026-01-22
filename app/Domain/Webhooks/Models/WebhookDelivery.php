<?php

namespace App\Domain\Webhooks\Models;

use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookDelivery extends Model
{
    protected $fillable = [
        'merchant_site_id',
        'event_type',
        'payload_json',
        'status',
        'attempts',
        'next_attempt_at',
        'last_error',
    ];

    protected $casts = [
        'payload_json' => 'array',
        'attempts' => 'integer',
        'next_attempt_at' => 'datetime',
    ];

    public function merchantSite(): BelongsTo
    {
        return $this->belongsTo(MerchantSite::class);
    }
}
