<?php

namespace App\Http\Controllers\Portal;

use App\Domain\Credit\Services\CreditTierService;
use App\Domain\Payments\Models\Payment;
use App\Domain\Mandates\Models\Mandate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends PortalBaseController
{
    public function __construct(private readonly CreditTierService $creditTierService)
    {
    }

    public function index(): View|RedirectResponse
    {
        $customer = $this->requireCustomer();
        if ($customer instanceof RedirectResponse) {
            return $customer;
        }

        $profile = $customer->creditProfile;
        $effectiveLimit = $profile ? $this->creditTierService->getEffectiveLimit($profile) : null;
        $effectiveDays = $profile ? $this->creditTierService->getEffectiveDaysMax($profile) : null;

        return view('portal.dashboard', [
            'customer' => $customer,
            'profile' => $profile,
            'effectiveLimit' => $effectiveLimit,
            'effectiveDays' => $effectiveDays,
        ]);
    }

    public function payments(): View|RedirectResponse
    {
        $customer = $this->requireCustomer();
        if ($customer instanceof RedirectResponse) {
            return $customer;
        }

        $payments = Payment::where('customer_id', $customer->id)
            ->orderByDesc('id')
            ->get();

        return view('portal.payments', [
            'customer' => $customer,
            'payments' => $payments,
        ]);
    }

    public function mandates(): View|RedirectResponse
    {
        $customer = $this->requireCustomer();
        if ($customer instanceof RedirectResponse) {
            return $customer;
        }

        $mandates = Mandate::where('customer_id', $customer->id)
            ->orderByDesc('id')
            ->get();

        return view('portal.mandates', [
            'customer' => $customer,
            'mandates' => $mandates,
        ]);
    }
}
