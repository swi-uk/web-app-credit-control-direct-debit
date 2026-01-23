<?php

namespace App\Domain\Partners\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partner extends Model
{
    protected $fillable = [
        'name',
        'status',
    ];

    public function merchants(): HasMany
    {
        return $this->hasMany(PartnerMerchant::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(PartnerCommission::class);
    }
}
