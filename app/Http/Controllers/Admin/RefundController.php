<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Audit\Models\AuditEvent;
use App\Domain\Refunds\Models\RefundRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class RefundController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $query = RefundRequest::with(['customer', 'payment'])->orderByDesc('id');
        if ($status) {
            $query->where('status', $status);
        }

        $refunds = $query->get();

        return view('admin.refunds.index', [
            'refunds' => $refunds,
            'status' => $status,
        ]);
    }

    public function approve(RefundRequest $refundRequest): RedirectResponse
    {
        $refundRequest->status = 'approved';
        $refundRequest->decided_at = now();
        $refundRequest->save();

        $this->audit($refundRequest, 'refund.approved');
        $this->sendEmail($refundRequest, 'emails.refund_approved', 'Refund approved');

        return redirect()->route('admin.refunds.index');
    }

    public function deny(Request $request, RefundRequest $refundRequest): RedirectResponse
    {
        $refundRequest->status = 'denied';
        $refundRequest->admin_note = $request->input('admin_note');
        $refundRequest->decided_at = now();
        $refundRequest->save();

        $this->audit($refundRequest, 'refund.denied');

        return redirect()->route('admin.refunds.index');
    }

    public function markProcessed(RefundRequest $refundRequest): RedirectResponse
    {
        $refundRequest->status = 'processed';
        $refundRequest->processed_at = now();
        $refundRequest->save();

        $this->audit($refundRequest, 'refund.processed');

        return redirect()->route('admin.refunds.index');
    }

    private function audit(RefundRequest $refund, string $event): void
    {
        AuditEvent::create([
            'merchant_id' => $refund->merchant_id,
            'customer_id' => $refund->customer_id,
            'event_type' => $event,
            'message' => null,
            'payload_json' => ['refund_id' => $refund->id],
            'created_at' => now(),
        ]);
    }

    private function sendEmail(RefundRequest $refund, string $view, string $subject): void
    {
        $customer = $refund->customer;
        if (!$customer?->email) {
            return;
        }

        Mail::send($view, [
            'refund' => $refund,
            'customer' => $customer,
        ], function ($message) use ($customer, $subject) {
            $message->to($customer->email)->subject($subject);
        });
    }
}
