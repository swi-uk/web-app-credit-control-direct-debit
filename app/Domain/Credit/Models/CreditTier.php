<?php

namespace App\Domain\Credit\Models;

use App\Domain\Merchants\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreditTier extends Model
{
    protected $fillable = [
        'merchant_id',
        'name',
        'max_exposure_amount',
        'max_days',
        'priority',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'max_exposure_amount' => 'decimal:2',
        'max_days' => 'integer',
        'priority' => 'integer',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function rules(): HasMany
    {
        return $this->hasMany(CreditTierRule::class, 'tier_id');
    }
}
