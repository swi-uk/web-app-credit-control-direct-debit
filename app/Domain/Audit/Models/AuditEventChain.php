<?php

namespace App\Domain\Audit\Models;

use App\Domain\Merchants\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditEventChain extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'merchant_id',
        'last_hash',
        'updated_at',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
