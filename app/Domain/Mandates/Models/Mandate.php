<?php

namespace App\Domain\Mandates\Models;

use App\Domain\Customers\Models\Customer;
use App\Domain\Merchants\Models\Merchant;
use App\Domain\Payments\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mandate extends Model
{
    protected $fillable = [
        'merchant_id',
        'customer_id',
        'reference',
        'account_holder_name',
        'sort_code',
        'account_number',
        'bank_address_json',
        'consent_timestamp',
        'consent_ip',
        'consent_user_agent',
        'status',
    ];

    protected $casts = [
        'account_holder_name' => 'encrypted',
        'sort_code' => 'encrypted',
        'account_number' => 'encrypted',
        'bank_address_json' => 'array',
        'consent_timestamp' => 'datetime',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
