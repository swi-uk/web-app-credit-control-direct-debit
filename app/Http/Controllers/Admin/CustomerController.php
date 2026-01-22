<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Customers\Models\Customer;
use App\Domain\Customers\Models\CreditProfile;
use App\Domain\Integrations\Models\ExternalLink;
use App\Domain\Webhooks\Services\WebhookOutboxService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(private readonly WebhookOutboxService $webhookOutboxService)
    {
    }

    public function index(): View
    {
        $customers = Customer::with('creditProfile')->orderBy('email')->get();

        return view('admin.customers.index', [
            'customers' => $customers,
        ]);
    }

    public function edit(Customer $customer): View
    {
        $customer->load('creditProfile');
        $externalLinks = ExternalLink::with('merchantSite')
            ->where('entity_type', 'customer')
            ->where('entity_id', $customer->id)
            ->orderBy('merchant_site_id')
            ->get();

        return view('admin.customers.edit', [
            'customer' => $customer,
            'externalLinks' => $externalLinks,
        ]);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string'],
            'limit_amount' => ['required', 'numeric'],
            'days_max' => ['required', 'integer'],
            'days_default' => ['nullable', 'integer'],
            'lock_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $customer->status = $validated['status'];
        $customer->lock_reason = $validated['lock_reason'] ?? null;
        $customer->save();

        $creditProfile = $customer->creditProfile ?: new CreditProfile(['customer_id' => $customer->id]);
        $creditProfile->limit_amount = $validated['limit_amount'];
        $creditProfile->days_max = $validated['days_max'];
        if (array_key_exists('days_default', $validated) && $validated['days_default'] !== null) {
            $creditProfile->days_default = $validated['days_default'];
        } elseif (!$creditProfile->days_default) {
            $creditProfile->days_default = 14;
        }
        $creditProfile->save();

        $customer->merchant?->sites?->each(function ($site) use ($customer, $creditProfile) {
            $externalLink = ExternalLink::where('merchant_site_id', $site->id)
                ->where('entity_type', 'customer')
                ->where('entity_id', $customer->id)
                ->first();
            $externalUserId = $externalLink?->external_id;
            $externalType = $externalLink?->external_type ?? 'user';
            $legacyWooUserId = $site->platform === 'woocommerce' ? $externalUserId : null;
            $payload = [
                'type' => 'customer.credit.update',
                'data' => [
                    'external_customer_id' => $externalUserId,
                    'external_customer_type' => $externalType,
                    'woocommerce_user_id' => $legacyWooUserId,
                    'credit_status' => $customer->status,
                    'credit_limit_amount' => $creditProfile->limit_amount,
                    'credit_days_max' => $creditProfile->days_max,
                    'current_exposure' => $creditProfile->current_exposure_amount,
                    'lock_reason' => $customer->lock_reason ?? '',
                ],
            ];
            $this->webhookOutboxService->enqueue($site, 'customer.credit.update', $payload);
        });

        return redirect()->route('admin.customers.edit', $customer);
    }
}
