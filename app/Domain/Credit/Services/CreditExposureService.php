<?php

namespace App\Domain\Credit\Services;

use App\Domain\Customers\Models\Customer;
use App\Domain\Payments\Models\Payment;

class CreditExposureService
{
    private const STATUSES = [
        'scheduled',
        'submitted',
        'processing',
        'retry_scheduled',
    ];

    public function recalculate(Customer $customer): void
    {
        $exposure = Payment::where('customer_id', $customer->id)
            ->whereIn('status', self::STATUSES)
            ->sum('amount');

        $creditProfile = $customer->creditProfile;
        if (!$creditProfile) {
            return;
        }

        $creditProfile->current_exposure_amount = $exposure;
        $creditProfile->save();
    }
}
