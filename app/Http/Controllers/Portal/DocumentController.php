<?php

namespace App\Http\Controllers\Portal;

use App\Domain\Documents\Models\Document;
use App\Domain\Documents\Services\DocumentService;
use App\Domain\Mandates\Models\Mandate;
use App\Domain\Payments\Models\Payment;
use App\Domain\Refunds\Models\RefundRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class DocumentController extends PortalBaseController
{
    public function __construct(private readonly DocumentService $documentService)
    {
    }

    public function download(Document $document): Response|RedirectResponse
    {
        $customer = $this->requireCustomer();
        if ($customer instanceof RedirectResponse) {
            return $customer;
        }

        if ($document->customer_id !== $customer->id) {
            return redirect()->route('portal.dashboard');
        }

        $content = Storage::disk('local')->get($document->file_path);

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($document->file_path) . '"',
        ]);
    }

    public function mandateReceipt(Mandate $mandate): Response|RedirectResponse
    {
        $customer = $this->requireCustomer();
        if ($customer instanceof RedirectResponse) {
            return $customer;
        }
        if ($mandate->customer_id !== $customer->id) {
            return redirect()->route('portal.dashboard');
        }

        $document = $this->documentService->generateMandateReceipt($mandate);
        return $this->download($document);
    }

    public function advanceNotice(Payment $payment): Response|RedirectResponse
    {
        $customer = $this->requireCustomer();
        if ($customer instanceof RedirectResponse) {
            return $customer;
        }
        if ($payment->customer_id !== $customer->id) {
            return redirect()->route('portal.dashboard');
        }

        $document = $this->documentService->generateAdvanceNotice($payment);
        return $this->download($document);
    }

    public function unpaidNotice(Payment $payment): Response|RedirectResponse
    {
        $customer = $this->requireCustomer();
        if ($customer instanceof RedirectResponse) {
            return $customer;
        }
        if ($payment->customer_id !== $customer->id) {
            return redirect()->route('portal.dashboard');
        }

        $document = $this->documentService->generateUnpaidNotice($payment);
        return $this->download($document);
    }

    public function refundNotice(RefundRequest $refundRequest): Response|RedirectResponse
    {
        $customer = $this->requireCustomer();
        if ($customer instanceof RedirectResponse) {
            return $customer;
        }
        if ($refundRequest->customer_id !== $customer->id) {
            return redirect()->route('portal.dashboard');
        }

        $document = $this->documentService->generateRefundNotice($refundRequest);
        return $this->download($document);
    }
}
