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
        'api_key_hash',
        'webhook_secret',
    ];

    protected $casts = [
        'webhook_secret' => 'encrypted',
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
