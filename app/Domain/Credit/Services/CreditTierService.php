<?php

namespace App\Domain\Credit\Services;

use App\Domain\Credit\Models\CreditTier;
use App\Domain\Customers\Models\CreditProfile;
use App\Domain\Customers\Models\Customer;

class CreditTierService
{
    public function assignTier(Customer $customer): void
    {
        $profile = $customer->creditProfile;
        if (!$profile || $profile->manual_tier_override) {
            return;
        }

        $tiers = CreditTier::with('rules')
            ->where('merchant_id', $customer->merchant_id)
            ->where('is_active', true)
            ->orderBy('priority')
            ->get();

        if ($tiers->isEmpty()) {
            return;
        }

        $defaultTier = $tiers->firstWhere('is_default', true) ?? $tiers->first();
        $selectedTier = $defaultTier;

        $accountAgeDays = $customer->created_at ? $customer->created_at->diffInDays(now()) : 0;

        foreach ($tiers as $tier) {
            $rule = $tier->rules->first();
            if (!$rule) {
                $selectedTier = $tier;
                continue;
            }

            if ($profile->successful_collections < $rule->min_successful_collections) {
                continue;
            }
            if ($profile->bounces_60d > $rule->max_bounces_60d) {
                continue;
            }
            if ($accountAgeDays < $rule->min_account_age_days) {
                continue;
            }

            $selectedTier = $tier;
        }

        if ($profile->credit_tier_id !== $selectedTier->id) {
            $profile->credit_tier_id = $selectedTier->id;
            $profile->tier_assigned_at = now();
            $profile->save();
        }
    }

    public function getEffectiveLimit(CreditProfile $profile): string
    {
        if ($profile->manual_limit_override) {
            return (string) $profile->limit_amount;
        }

        if ($profile->creditTier) {
            return (string) $profile->creditTier->max_exposure_amount;
        }

        return (string) $profile->limit_amount;
    }

    public function getEffectiveDaysMax(CreditProfile $profile): int
    {
        if ($profile->manual_days_override) {
            return (int) $profile->days_max;
        }

        if ($profile->creditTier) {
            return (int) $profile->creditTier->max_days;
        }

        return (int) $profile->days_max;
    }
}
