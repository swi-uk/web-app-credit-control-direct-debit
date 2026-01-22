<?php

namespace App\Domain\Customers\Services;

use App\Domain\Audit\Models\AuditEvent;
use App\Domain\Customers\Models\CreditProfile;
use App\Domain\Customers\Models\Customer;
use App\Domain\Merchants\Models\Merchant;
use Illuminate\Support\Arr;

class CustomerService
{
    public function upsertFromWoo(Merchant $merchant, array $payload): Customer
    {
        $email = $payload['email'] ?? null;

        $customer = Customer::where('merchant_id', $merchant->id)
            ->where('email', $email)
            ->first();

        if (!$customer) {
            $customer = new Customer([
                'merchant_id' => $merchant->id,
                'email' => $email,
                'status' => 'active',
            ]);
        }

        $customer->phone = $payload['phone'] ?? $customer->phone;
        $billing = $payload['billing'] ?? [];
        if (is_array($billing)) {
            $customer->first_name = Arr::get($billing, 'first_name', $customer->first_name);
            $customer->last_name = Arr::get($billing, 'last_name', $customer->last_name);
            $customer->billing_address_json = $billing;
        }

        $incomingWooId = $payload['woocommerce_user_id'] ?? null;
        if ($incomingWooId) {
            if (!$customer->external_woocommerce_user_id) {
                $customer->external_woocommerce_user_id = $incomingWooId;
            } elseif ((string) $customer->external_woocommerce_user_id !== (string) $incomingWooId) {
                AuditEvent::create([
                    'merchant_id' => $merchant->id,
                    'customer_id' => $customer->id,
                    'event_type' => 'woocommerce_user_id_mismatch',
                    'message' => 'Attempted to overwrite existing WooCommerce user id.',
                    'payload_json' => [
                        'existing' => $customer->external_woocommerce_user_id,
                        'incoming' => $incomingWooId,
                    ],
                    'created_at' => now(),
                ]);
            }
        }

        $customer->save();

        $this->ensureCreditProfile($customer);

        return $customer->fresh(['creditProfile']);
    }

    private function ensureCreditProfile(Customer $customer): CreditProfile
    {
        $creditProfile = $customer->creditProfile;
        if (!$creditProfile) {
            $creditProfile = new CreditProfile([
                'customer_id' => $customer->id,
                'limit_amount' => 250,
                'current_exposure_amount' => 0,
                'days_max' => 14,
                'days_default' => 14,
            ]);
            $creditProfile->save();
        }

        return $creditProfile;
    }
}
