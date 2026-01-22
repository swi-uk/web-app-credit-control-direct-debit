<?php

namespace App\Domain\Security\Models;

use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchantSiteSecret extends Model
{
    protected $fillable = [
        'merchant_site_id',
        'sftp_private_key',
        'sftp_password',
        'api_token',
    ];

    protected $casts = [
        'sftp_private_key' => 'encrypted',
        'sftp_password' => 'encrypted',
        'api_token' => 'encrypted',
    ];

    public function merchantSite(): BelongsTo
    {
        return $this->belongsTo(MerchantSite::class);
    }
}
