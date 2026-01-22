<?php

namespace App\Domain\Credit\Services;

use App\Domain\Customers\Models\CreditProfile;
use App\Domain\Customers\Models\Customer;

class CreditDecisionService
{
    public function denyReason(Customer $customer, CreditProfile $creditProfile, float $amount): ?string
    {
        if (in_array($customer->status, ['locked', 'blocked'], true)) {
            return $customer->status;
        }

        $currentExposure = (float) $creditProfile->current_exposure_amount;
        $limit = (float) $creditProfile->limit_amount;
        if (($currentExposure + $amount) > $limit) {
            return 'limit_exceeded';
        }

        return null;
    }
}
