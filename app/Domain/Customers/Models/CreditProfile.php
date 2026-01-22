<?php

namespace App\Domain\Customers\Models;

use App\Domain\Customers\Models\Customer;
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
    ];

    protected $casts = [
        'limit_amount' => 'decimal:2',
        'current_exposure_amount' => 'decimal:2',
        'days_max' => 'integer',
        'days_default' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
