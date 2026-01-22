<?php

namespace App\Domain\Security\Models;

use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchantSiteSecret extends Model
{
    protected $fillable = [
        'merchant_site_id',
        'key',
        'encrypted_value',
    ];

    protected $casts = [
        'encrypted_value' => 'encrypted',
    ];

    public static function valueFor(int $merchantSiteId, string $key): ?string
    {
        return static::where('merchant_site_id', $merchantSiteId)
            ->where('key', $key)
            ->value('encrypted_value');
    }

    public function merchantSite(): BelongsTo
    {
        return $this->belongsTo(MerchantSite::class);
    }
}
