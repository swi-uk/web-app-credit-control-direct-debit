<?php

namespace App\Domain\Submission\Models;

use App\Domain\Merchants\Models\Merchant;
use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubmissionBatch extends Model
{
    protected $fillable = [
        'merchant_id',
        'merchant_site_id',
        'type',
        'status',
        'file_path',
        'external_ref',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function merchantSite(): BelongsTo
    {
        return $this->belongsTo(MerchantSite::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SubmissionItem::class);
    }
}
