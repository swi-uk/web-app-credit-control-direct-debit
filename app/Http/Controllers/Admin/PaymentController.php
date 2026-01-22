<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Audit\Models\AuditEvent;
use App\Domain\Credit\Services\CreditTierService;
use App\Domain\Payments\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private readonly CreditTierService $creditTierService)
    {
    }

    public function index(Request $request): View
    {
        $filter = $request->query('filter');
        $query = Payment::with(['customer', 'sourceSite'])->orderByDesc('id');

        if ($filter === 'today') {
            $query->whereDate('due_date', now()->toDateString());
        }

        if ($filter === 'week') {
            $query->whereBetween('due_date', [now()->toDateString(), now()->addDays(7)->toDateString()]);
        }

        if ($filter === 'bounced') {
            $query->whereIn('status', ['unpaid_returned', 'retry_scheduled', 'failed_final']);
        }

        $payments = $query->get();

        return view('admin.payments.index', [
            'payments' => $payments,
            'filter' => $filter,
        ]);
    }

    public function markCollected(Payment $payment): RedirectResponse
    {
        $payment->status = 'collected';
        $payment->save();

        $customer = $payment->customer;
        $customer->load('creditProfile');
        if ($customer->creditProfile) {
            $customer->creditProfile->successful_collections += 1;
            $customer->creditProfile->save();
            $this->creditTierService->assignTier($customer);
        }

        AuditEvent::create([
            'merchant_id' => $payment->merchant_id,
            'customer_id' => $payment->customer_id,
            'event_type' => 'payment.collected',
            'message' => 'Marked collected via admin.',
            'payload_json' => ['payment_id' => $payment->id],
            'created_at' => now(),
        ]);

        return redirect()->route('admin.payments.index');
    }
}
