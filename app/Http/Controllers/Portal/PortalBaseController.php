<?php

namespace App\Http\Controllers\Portal;

use App\Domain\Customers\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

abstract class PortalBaseController extends Controller
{
    protected function requireCustomer(): Customer|RedirectResponse
    {
        $customerId = session('portal_customer_id');
        if (!$customerId) {
            return redirect()->route('portal.login');
        }

        $customer = Customer::with('creditProfile')->find($customerId);
        if (!$customer) {
            session()->forget('portal_customer_id');
            return redirect()->route('portal.login');
        }

        return $customer;
    }
}
