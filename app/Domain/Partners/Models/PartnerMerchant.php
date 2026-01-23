<?php

namespace App\Domain\Partners\Models;

use App\Domain\Merchants\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerMerchant extends Model
{
    protected $fillable = [
        'partner_id',
        'merchant_id',
        'commission_type',
        'commission_value',
    ];

    protected $casts = [
        'commission_value' => 'decimal:4',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
