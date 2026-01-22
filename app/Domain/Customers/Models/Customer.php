<?php

namespace App\Domain\Customers\Models;

use App\Domain\Customers\Models\CreditProfile;
use App\Domain\Integrations\Models\ExternalLink;
use App\Domain\Mandates\Models\Mandate;
use App\Domain\Mandates\Models\MandateUpdateLink;
use App\Domain\Portal\Models\CustomerPortalToken;
use App\Domain\Refunds\Models\RefundRequest;
use App\Domain\Documents\Models\Document;
use App\Domain\Merchants\Models\Merchant;
use App\Domain\Payments\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    protected $fillable = [
        'merchant_id',
        'email',
        'phone',
        'first_name',
        'last_name',
        'billing_address_json',
        'status',
        'lock_reason',
        'locked_at',
    ];

    protected $casts = [
        'billing_address_json' => 'array',
        'locked_at' => 'datetime',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function creditProfile(): HasOne
    {
        return $this->hasOne(CreditProfile::class);
    }

    public function mandates(): HasMany
    {
        return $this->hasMany(Mandate::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function externalLinks(): HasMany
    {
        return $this->hasMany(ExternalLink::class, 'entity_id')
            ->where('entity_type', 'customer');
    }

    public function portalTokens(): HasMany
    {
        return $this->hasMany(CustomerPortalToken::class);
    }

    public function refundRequests(): HasMany
    {
        return $this->hasMany(RefundRequest::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function mandateUpdateLinks(): HasMany
    {
        return $this->hasMany(MandateUpdateLink::class);
    }
}
