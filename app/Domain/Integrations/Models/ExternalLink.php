<?php

namespace App\Domain\Integrations\Models;

use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalLink extends Model
{
    protected $fillable = [
        'merchant_site_id',
        'entity_type',
        'entity_id',
        'external_type',
        'external_id',
        'external_key',
        'meta_json',
    ];

    protected $casts = [
        'meta_json' => 'array',
    ];

    public function merchantSite(): BelongsTo
    {
        return $this->belongsTo(MerchantSite::class);
    }
}
