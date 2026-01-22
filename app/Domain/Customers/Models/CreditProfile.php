<?php

namespace App\Domain\Customers\Models;

use App\Domain\Customers\Models\Customer;
use App\Domain\Credit\Models\CreditTier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditProfile extends Model
{
    protected $fillable = [
        'customer_id',
        'limit_amount',
        'current_exposure_amount',
        'days_max',
        'days_default',
        'lock_reason',
        'credit_tier_id',
        'manual_tier_override',
        'manual_limit_override',
        'manual_days_override',
        'tier_assigned_at',
        'successful_collections',
        'bounces_60d',
    ];

    protected $casts = [
        'limit_amount' => 'decimal:2',
        'current_exposure_amount' => 'decimal:2',
        'days_max' => 'integer',
        'days_default' => 'integer',
        'manual_tier_override' => 'boolean',
        'manual_limit_override' => 'boolean',
        'manual_days_override' => 'boolean',
        'tier_assigned_at' => 'datetime',
        'successful_collections' => 'integer',
        'bounces_60d' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creditTier(): BelongsTo
    {
        return $this->belongsTo(CreditTier::class, 'credit_tier_id');
    }
}
