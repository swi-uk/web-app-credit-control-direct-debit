<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Billing\Models\Invoice;
use App\Domain\Billing\Models\Plan;
use App\Domain\Billing\Models\MerchantSubscription;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function index(): View
    {
        return view('admin.billing.index', [
            'plans' => Plan::orderBy('code')->get(),
            'subscriptions' => MerchantSubscription::with('merchant', 'plan')->orderByDesc('id')->get(),
            'invoices' => Invoice::with('merchant')->orderByDesc('id')->get(),
        ]);
    }
}
