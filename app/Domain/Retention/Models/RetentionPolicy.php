<?php

namespace App\Domain\Retention\Models;

use App\Domain\Merchants\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RetentionPolicy extends Model
{
    protected $fillable = [
        'merchant_id',
        'mandates_retention_days',
        'payments_retention_days',
        'documents_retention_days',
        'pii_mask_after_days',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
