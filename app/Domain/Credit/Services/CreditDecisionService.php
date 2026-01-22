<?php

namespace App\Domain\Credit\Services;

use App\Domain\Customers\Models\CreditProfile;
use App\Domain\Customers\Models\Customer;

class CreditDecisionService
{
    public function __construct(private readonly CreditTierService $creditTierService)
    {
    }

    public function evaluate(Customer $customer, CreditProfile $creditProfile, float $amount): array
    {
        if (in_array($customer->status, ['locked', 'blocked', 'restricted'], true)) {
            return $this->decisionResult($creditProfile, $amount, $customer->status);
        }

        $currentExposure = (float) $creditProfile->current_exposure_amount;
        $effectiveLimit = (float) $this->creditTierService->getEffectiveLimit($creditProfile);
        $effectiveDaysMax = $this->creditTierService->getEffectiveDaysMax($creditProfile);
        $remaining = $effectiveLimit - $currentExposure - $amount;

        if (($currentExposure + $amount) > $effectiveLimit) {
            return $this->decisionResult($creditProfile, $amount, 'limit_exceeded');
        }

        return [
            'allowed' => true,
            'reason' => null,
            'tier_name' => $creditProfile->creditTier?->name,
            'effective_limit' => (string) $effectiveLimit,
            'effective_days_max' => $effectiveDaysMax,
            'remaining_credit' => (string) max(0, $remaining),
        ];
    }

    public function denyReason(Customer $customer, CreditProfile $creditProfile, float $amount): ?string
    {
        $decision = $this->evaluate($customer, $creditProfile, $amount);
        return $decision['reason'];
    }

    private function decisionResult(CreditProfile $profile, float $amount, string $reason): array
    {
        $effectiveLimit = (float) $this->creditTierService->getEffectiveLimit($profile);
        $effectiveDaysMax = $this->creditTierService->getEffectiveDaysMax($profile);
        $currentExposure = (float) $profile->current_exposure_amount;
        $remaining = $effectiveLimit - $currentExposure - $amount;

        return [
            'allowed' => false,
            'reason' => $reason,
            'tier_name' => $profile->creditTier?->name,
            'effective_limit' => (string) $effectiveLimit,
            'effective_days_max' => $effectiveDaysMax,
            'remaining_credit' => (string) max(0, $remaining),
        ];
    }
}
