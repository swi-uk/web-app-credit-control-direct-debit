<?php

namespace App\Domain\Orders\Models;

use App\Domain\Customers\Models\Customer;
use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderLink extends Model
{
    protected $fillable = [
        'merchant_site_id',
        'customer_id',
        'woo_order_id',
        'woo_order_key',
        'amount',
        'currency',
        'redirect_token_hash',
        'return_success_url',
        'return_cancel_url',
        'status',
        'used_at',
        'expires_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function merchantSite(): BelongsTo
    {
        return $this->belongsTo(MerchantSite::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
