<?php

namespace App\Domain\Credit\Models;

use App\Domain\Merchants\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditTierRule extends Model
{
    protected $fillable = [
        'merchant_id',
        'tier_id',
        'min_successful_collections',
        'max_bounces_60d',
        'min_account_age_days',
    ];

    protected $casts = [
        'min_successful_collections' => 'integer',
        'max_bounces_60d' => 'integer',
        'min_account_age_days' => 'integer',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function tier(): BelongsTo
    {
        return $this->belongsTo(CreditTier::class, 'tier_id');
    }
}
