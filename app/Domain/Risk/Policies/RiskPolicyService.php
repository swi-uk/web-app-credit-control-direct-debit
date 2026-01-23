<?php

namespace App\Domain\Risk\Policies;

use App\Domain\Credit\Services\CreditTierService;
use App\Domain\Customers\Models\Customer;
use App\Domain\Risk\Models\RiskScore;

class RiskPolicyService
{
    public function __construct(private readonly CreditTierService $creditTierService)
    {
    }

    public function apply(Customer $customer, RiskScore $score): void
    {
        $profile = $customer->creditProfile;
        if (!$profile) {
            return;
        }

        if ($score->band === 'critical') {
            $customer->status = 'locked';
            $customer->lock_reason = 'Risk policy: critical';
            $customer->locked_at = now();
            $customer->save();
            return;
        }

        if ($score->band === 'high') {
            $profile->manual_days_override = true;
            $profile->days_max = min($profile->days_max ?? 14, 7);
            $profile->save();
        }

        $this->creditTierService->assignTier($customer);
    }
}
