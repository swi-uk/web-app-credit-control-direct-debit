<?php

namespace App\Domain\Audit\Models;

use App\Domain\Customers\Models\Customer;
use App\Domain\Merchants\Models\Merchant;
use App\Domain\Audit\Models\AuditEventChain;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'merchant_id',
        'customer_id',
        'event_type',
        'message',
        'payload_json',
        'created_at',
    ];

    protected $casts = [
        'payload_json' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (AuditEvent $event) {
            $merchantId = $event->merchant_id;
            $chain = $merchantId ? AuditEventChain::firstOrCreate(
                ['merchant_id' => $merchantId],
                ['last_hash' => null, 'updated_at' => now()]
            ) : null;

            $prevHash = $chain?->last_hash;
            $event->prev_hash = $prevHash;
            $event->audit_hash = hash('sha256', json_encode([
                'prev' => $prevHash,
                'merchant_id' => $event->merchant_id,
                'customer_id' => $event->customer_id,
                'event_type' => $event->event_type,
                'message' => $event->message,
                'payload' => $event->payload_json,
                'created_at' => $event->created_at?->toDateTimeString(),
            ]));
        });

        static::created(function (AuditEvent $event) {
            if ($event->merchant_id) {
                AuditEventChain::updateOrCreate(
                    ['merchant_id' => $event->merchant_id],
                    ['last_hash' => $event->audit_hash, 'updated_at' => now()]
                );
            }
        });
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
