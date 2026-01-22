<?php

namespace App\Domain\Merchants\Models;

use App\Domain\Webhooks\Models\WebhookDelivery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MerchantSite extends Model
{
    protected $fillable = [
        'merchant_id',
        'site_id',
        'base_url',
        'platform',
        'api_key_hash',
        'webhook_secret',
        'capabilities',
        'settings_json',
    ];

    protected $casts = [
        'webhook_secret' => 'encrypted',
        'capabilities' => 'array',
        'settings_json' => 'array',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function webhookDeliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class);
    }
}
