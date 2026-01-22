<?php

namespace App\Domain\Payments\Models;

use App\Domain\Customers\Models\Customer;
use App\Domain\Mandates\Models\Mandate;
use App\Domain\Merchants\Models\Merchant;
use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'merchant_id',
        'customer_id',
        'mandate_id',
        'source_site_id',
        'external_order_id',
        'external_order_key',
        'external_order_type',
        'amount',
        'currency',
        'due_date',
        'status',
        'retry_count',
        'advance_notice_sent_at',
        'advance_notice_channel',
        'failure_code',
        'failure_description',
        'reported_at',
        'next_retry_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'retry_count' => 'integer',
        'advance_notice_sent_at' => 'datetime',
        'reported_at' => 'datetime',
        'next_retry_at' => 'datetime',
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

    public function sourceSite(): BelongsTo
    {
        return $this->belongsTo(MerchantSite::class, 'source_site_id');
    }
}
