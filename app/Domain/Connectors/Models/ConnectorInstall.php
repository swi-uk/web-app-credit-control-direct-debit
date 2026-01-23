<?php

namespace App\Domain\Connectors\Models;

use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConnectorInstall extends Model
{
    protected $fillable = [
        'merchant_site_id',
        'connector',
        'status',
        'access_token',
        'refresh_token',
        'installed_at',
    ];

    protected $casts = [
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
        'installed_at' => 'datetime',
    ];

    public function merchantSite(): BelongsTo
    {
        return $this->belongsTo(MerchantSite::class);
    }
}
