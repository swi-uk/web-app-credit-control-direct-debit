<?php

namespace App\Domain\Observability\Models;

use Illuminate\Database\Eloquent\Model;

class RequestLog extends Model
{
    protected $fillable = [
        'correlation_id',
        'method',
        'path',
        'status_code',
        'merchant_site_id',
        'ip',
        'user_agent',
        'duration_ms',
    ];
}
