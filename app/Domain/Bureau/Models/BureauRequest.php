<?php

namespace App\Domain\Bureau\Models;

use Illuminate\Database\Eloquent\Model;

class BureauRequest extends Model
{
    protected $fillable = [
        'merchant_site_id',
        'entity_type',
        'entity_id',
        'request_type',
        'idempotency_key',
        'request_json',
        'response_json',
        'http_status',
        'status',
        'last_error',
    ];

    protected $casts = [
        'request_json' => 'array',
        'response_json' => 'array',
    ];
}
