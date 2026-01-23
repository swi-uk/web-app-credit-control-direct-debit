<?php

namespace App\Domain\Merchants\Models;

use App\Domain\Webhooks\Models\WebhookDelivery;
use App\Domain\Security\Models\MerchantSiteApiKey;
use App\Domain\Security\Models\MerchantSiteSecret;
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
        'mode',
        'api_key_hash',
        'webhook_secret',
        'capabilities',
        'settings_json',
        'previous_webhook_secret',
        'webhook_secret_rotated_at',
    ];

    protected $casts = [
        'webhook_secret' => 'encrypted',
        'previous_webhook_secret' => 'encrypted',
        'capabilities' => 'array',
        'settings_json' => 'array',
        'webhook_secret_rotated_at' => 'datetime',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function webhookDeliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(MerchantSiteApiKey::class);
    }

    public function secrets(): HasMany
    {
        return $this->hasMany(MerchantSiteSecret::class);
    }
}
