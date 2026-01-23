<?php

namespace App\Domain\Merchants\Models;

use App\Domain\Billing\Models\Invoice;
use App\Domain\Billing\Models\MerchantSubscription;
use App\Domain\Billing\Models\UsageRecord;
use App\Domain\Billing\Models\MerchantContact;
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
        'settings_json',
        'branding_json',
    ];

    protected $casts = [
        'settings_json' => 'array',
        'branding_json' => 'array',
    ];

    public function sites(): HasMany
    {
        return $this->hasMany(MerchantSite::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(MerchantSubscription::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function usageRecords(): HasMany
    {
        return $this->hasMany(UsageRecord::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(MerchantContact::class);
    }
}
