<?php

namespace App\Http\Controllers\Portal;

use App\Domain\Audit\Models\AuditEvent;
use App\Domain\Payments\Models\Payment;
use App\Domain\Refunds\Models\RefundRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RefundController extends PortalBaseController
{
    public function create(Request $request): View|RedirectResponse
    {
        $customer = $this->requireCustomer();
        if ($customer instanceof RedirectResponse) {
            return $customer;
        }

        $payment = null;
        if ($request->filled('payment_id')) {
            $payment = Payment::where('customer_id', $customer->id)
                ->where('id', $request->input('payment_id'))
                ->first();
        }

        return view('portal.refunds.create', [
            'customer' => $customer,
            'payment' => $payment,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $customer = $this->requireCustomer();
        if ($customer instanceof RedirectResponse) {
            return $customer;
        }

        $validated = $request->validate([
            'payment_id' => ['nullable', 'exists:payments,id'],
            'amount_requested' => ['nullable', 'numeric'],
            'reason' => ['required', 'string'],
        ]);

        $payment = null;
        if (!empty($validated['payment_id'])) {
            $payment = Payment::where('customer_id', $customer->id)
                ->where('id', $validated['payment_id'])
                ->first();
        }

        $refund = RefundRequest::create([
            'merchant_id' => $customer->merchant_id,
            'customer_id' => $customer->id,
            'payment_id' => $payment?->id,
            'external_order_id' => $payment?->external_order_id,
            'amount_requested' => $validated['amount_requested'] ?? $payment?->amount,
            'reason' => $validated['reason'],
            'status' => 'requested',
        ]);

        AuditEvent::create([
            'merchant_id' => $customer->merchant_id,
            'customer_id' => $customer->id,
            'event_type' => 'refund.requested',
            'message' => null,
            'payload_json' => [
                'refund_id' => $refund->id,
                'payment_id' => $payment?->id,
            ],
            'created_at' => now(),
        ]);

        return redirect()->route('portal.payments');
    }
}
