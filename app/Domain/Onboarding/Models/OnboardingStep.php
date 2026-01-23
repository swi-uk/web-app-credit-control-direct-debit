<?php

namespace App\Domain\Onboarding\Models;

use App\Domain\Merchants\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingStep extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'merchant_id',
        'step_key',
        'status',
        'metadata_json',
        'updated_at',
    ];

    protected $casts = [
        'metadata_json' => 'array',
        'updated_at' => 'datetime',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
