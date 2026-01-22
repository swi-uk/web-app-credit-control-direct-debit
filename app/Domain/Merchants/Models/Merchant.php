<?php

namespace App\Domain\Merchants\Models;

use App\Domain\Customers\Models\Customer;
use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Merchant extends Model
{
    protected $fillable = [
        'name',
        'plan',
        'status',
    ];

    public function sites(): HasMany
    {
        return $this->hasMany(MerchantSite::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
