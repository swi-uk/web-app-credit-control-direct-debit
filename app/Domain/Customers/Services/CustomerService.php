<?php

namespace App\Domain\Customers\Services;

use App\Domain\Audit\Models\AuditEvent;
use App\Domain\Customers\Models\CreditProfile;
use App\Domain\Customers\Models\Customer;
use App\Domain\Integrations\Models\ExternalLink;
use App\Domain\Merchants\Models\Merchant;
use App\Domain\Credit\Services\CreditTierService;
use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Support\Arr;

class CustomerService
{
    public function __construct(private readonly CreditTierService $creditTierService)
    {
    }
    public function upsertFromChannel(Merchant $merchant, MerchantSite $site, array $payload): Customer
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

        $customer->save();

        $externalCustomerId = $payload['external_customer_id'] ?? null;
        $externalCustomerType = $payload['external_customer_type'] ?? 'user';
        if ($externalCustomerId) {
            $this->setExternalUserLink($customer, $site, (string) $externalCustomerId, (string) $externalCustomerType);
        }

        $this->ensureCreditProfile($customer);
        $this->creditTierService->assignTier($customer);

        return $customer->fresh(['creditProfile']);
    }

    public function upsertFromWoo(Merchant $merchant, MerchantSite $site, array $payload): Customer
    {
        $payload['external_customer_type'] = $payload['external_customer_type'] ?? 'user';
        $payload['external_customer_id'] = $payload['woocommerce_user_id'] ?? null;

        return $this->upsertFromChannel($merchant, $site, $payload);
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
                'manual_tier_override' => false,
                'manual_limit_override' => false,
                'manual_days_override' => false,
                'successful_collections' => 0,
                'bounces_60d' => 0,
            ]);
            $creditProfile->save();
        }

        return $creditProfile;
    }

    public function setExternalUserLink(
        Customer $customer,
        MerchantSite $site,
        string $externalUserId,
        string $externalType = 'user'
    ): ?ExternalLink {
        $existingForCustomer = ExternalLink::where('merchant_site_id', $site->id)
            ->where('entity_type', 'customer')
            ->where('entity_id', $customer->id)
            ->where('external_type', $externalType)
            ->first();

        if ($existingForCustomer && $existingForCustomer->external_id !== $externalUserId) {
            $this->logExternalLinkMismatch($customer, $site, $existingForCustomer->external_id, $externalUserId);
            return $existingForCustomer;
        }

        $existingByExternal = ExternalLink::where('merchant_site_id', $site->id)
            ->where('external_type', $externalType)
            ->where('external_id', $externalUserId)
            ->first();

        if ($existingByExternal && $existingByExternal->entity_id !== $customer->id) {
            $this->logExternalLinkConflict($customer, $site, $externalUserId, $existingByExternal->entity_id);
            return $existingByExternal;
        }

        if ($existingForCustomer) {
            return $existingForCustomer;
        }

        return ExternalLink::create([
            'merchant_site_id' => $site->id,
            'entity_type' => 'customer',
            'entity_id' => $customer->id,
            'external_type' => $externalType,
            'external_id' => $externalUserId,
            'external_key' => null,
            'meta_json' => null,
        ]);
    }

    public function getExternalUserId(
        Customer $customer,
        MerchantSite $site,
        string $externalType = 'user'
    ): ?string {
        $link = ExternalLink::where('merchant_site_id', $site->id)
            ->where('entity_type', 'customer')
            ->where('entity_id', $customer->id)
            ->where('external_type', $externalType)
            ->first();

        return $link?->external_id;
    }

    private function logExternalLinkMismatch(
        Customer $customer,
        MerchantSite $site,
        string $existingId,
        string $incomingId
    ): void {
        AuditEvent::create([
            'merchant_id' => $customer->merchant_id,
            'customer_id' => $customer->id,
            'event_type' => 'external_user_id_mismatch',
            'message' => 'Attempted to overwrite existing external user id.',
            'payload_json' => [
                'site_id' => $site->site_id,
                'existing' => $existingId,
                'incoming' => $incomingId,
            ],
            'created_at' => now(),
        ]);
    }

    private function logExternalLinkConflict(
        Customer $customer,
        MerchantSite $site,
        string $externalUserId,
        int $existingEntityId
    ): void {
        AuditEvent::create([
            'merchant_id' => $customer->merchant_id,
            'customer_id' => $customer->id,
            'event_type' => 'external_user_id_conflict',
            'message' => 'External user id already linked to another customer.',
            'payload_json' => [
                'site_id' => $site->site_id,
                'external_id' => $externalUserId,
                'existing_customer_id' => $existingEntityId,
            ],
            'created_at' => now(),
        ]);
    }
}
