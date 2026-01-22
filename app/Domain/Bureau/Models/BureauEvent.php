<?php

namespace App\Domain\Bureau\Models;

use Illuminate\Database\Eloquent\Model;

class BureauEvent extends Model
{
    protected $fillable = [
        'merchant_site_id',
        'event_type',
        'external_ref',
        'entity_type',
        'entity_id',
        'occurred_at',
        'payload_json',
        'processed_at',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'payload_json' => 'array',
        'processed_at' => 'datetime',
    ];
}
