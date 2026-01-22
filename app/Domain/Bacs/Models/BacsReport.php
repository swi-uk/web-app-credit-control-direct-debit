<?php

namespace App\Domain\Bacs\Models;

use App\Domain\Merchants\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BacsReport extends Model
{
    protected $fillable = [
        'merchant_id',
        'type',
        'source',
        'original_filename',
        'file_path',
        'status',
        'processed_at',
        'remote_id',
        'file_hash',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BacsReportItem::class);
    }
}
