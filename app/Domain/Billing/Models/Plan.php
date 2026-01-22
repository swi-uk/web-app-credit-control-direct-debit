<?php

namespace App\Domain\Billing\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'code',
        'monthly_price',
        'included_mandates',
        'included_debits',
        'included_sms',
        'per_debit_fee',
        'per_mandate_fee',
        'per_sms_fee',
        'features_json',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'included_mandates' => 'integer',
        'included_debits' => 'integer',
        'included_sms' => 'integer',
        'per_debit_fee' => 'decimal:4',
        'per_mandate_fee' => 'decimal:4',
        'per_sms_fee' => 'decimal:4',
        'features_json' => 'array',
    ];
}
