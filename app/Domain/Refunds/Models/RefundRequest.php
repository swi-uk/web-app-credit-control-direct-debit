<?php

namespace App\Domain\Refunds\Models;

use App\Domain\Customers\Models\Customer;
use App\Domain\Merchants\Models\Merchant;
use App\Domain\Payments\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefundRequest extends Model
{
    protected $fillable = [
        'merchant_id',
        'customer_id',
        'payment_id',
        'external_order_id',
        'amount_requested',
        'reason',
        'status',
        'admin_note',
        'decided_at',
        'processed_at',
    ];

    protected $casts = [
        'amount_requested' => 'decimal:2',
        'decided_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
