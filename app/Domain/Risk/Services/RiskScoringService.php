<?php

namespace App\Domain\Risk\Services;

use App\Domain\Customers\Models\Customer;
use App\Domain\Risk\Models\RiskScore;

class RiskScoringService
{
    public function score(Customer $customer): RiskScore
    {
        $profile = $customer->creditProfile;
        $bounces = $profile?->bounces_60d ?? 0;
        $success = $profile?->successful_collections ?? 0;
        $exposure = (float) ($profile?->current_exposure_amount ?? 0);
        $limit = (float) ($profile?->limit_amount ?? 1);

        $utilization = $limit > 0 ? ($exposure / $limit) : 0;
        $score = 300;
        $score += (int) min(400, $bounces * 50);
        $score -= (int) min(200, $success * 10);
        $score += (int) min(200, $utilization * 200);

        $band = match (true) {
            $score >= 800 => 'critical',
            $score >= 600 => 'high',
            $score >= 400 => 'medium',
            default => 'low',
        };

        return RiskScore::create([
            'merchant_id' => $customer->merchant_id,
            'customer_id' => $customer->id,
            'score' => $score,
            'band' => $band,
            'factors_json' => [
                'bounces_60d' => $bounces,
                'successful_collections' => $success,
                'utilization' => $utilization,
            ],
            'calculated_at' => now(),
        ]);
    }
}
