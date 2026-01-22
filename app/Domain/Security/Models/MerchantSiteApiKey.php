<?php

namespace App\Domain\Security\Models;

use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchantSiteApiKey extends Model
{
    protected $fillable = [
        'merchant_site_id',
        'key_hash',
        'name',
        'status',
        'last_used_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    public function merchantSite(): BelongsTo
    {
        return $this->belongsTo(MerchantSite::class);
    }
}
