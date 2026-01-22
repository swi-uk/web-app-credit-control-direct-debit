<?php

namespace App\Domain\Payments\Models;

use App\Domain\Customers\Models\Customer;
use App\Domain\Mandates\Models\Mandate;
use App\Domain\Merchants\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'merchant_id',
        'customer_id',
        'mandate_id',
        'woo_order_id',
        'amount',
        'currency',
        'due_date',
        'status',
        'retry_count',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'retry_count' => 'integer',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function mandate(): BelongsTo
    {
        return $this->belongsTo(Mandate::class);
    }
}
