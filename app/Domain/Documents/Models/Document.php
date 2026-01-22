<?php

namespace App\Domain\Documents\Models;

use App\Domain\Customers\Models\Customer;
use App\Domain\Merchants\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'merchant_id',
        'customer_id',
        'type',
        'file_path',
        'sha256_hash',
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
