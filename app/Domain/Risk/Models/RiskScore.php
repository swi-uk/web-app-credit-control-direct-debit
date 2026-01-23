<?php

namespace App\Domain\Risk\Models;

use App\Domain\Customers\Models\Customer;
use App\Domain\Merchants\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskScore extends Model
{
    protected $fillable = [
        'merchant_id',
        'customer_id',
        'score',
        'band',
        'factors_json',
        'calculated_at',
    ];

    protected $casts = [
        'score' => 'integer',
        'factors_json' => 'array',
        'calculated_at' => 'datetime',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
