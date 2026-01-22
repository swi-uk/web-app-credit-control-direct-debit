<?php

namespace App\Domain\Documents\Services;

use App\Domain\Documents\Models\Document;
use App\Domain\Mandates\Models\Mandate;
use App\Domain\Payments\Models\Payment;
use App\Domain\Refunds\Models\RefundRequest;
use App\Domain\Customers\Models\Customer;
use App\Domain\Merchants\Models\Merchant;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function generateMandateReceipt(Mandate $mandate): Document
    {
        $customer = $mandate->customer;
        $merchant = $mandate->merchant;
        $html = view('documents.mandate_receipt', [
            'mandate' => $mandate,
            'customer' => $customer,
            'merchant' => $merchant,
        ])->render();

        return $this->storeDocument($merchant, $customer, 'mandate_receipt', $html, 'mandate_' . $mandate->id);
    }

    public function generateAdvanceNotice(Payment $payment): Document
    {
        $customer = $payment->customer;
        $merchant = $payment->merchant;
        $html = view('documents.advance_notice', [
            'payment' => $payment,
            'customer' => $customer,
            'merchant' => $merchant,
        ])->render();

        return $this->storeDocument($merchant, $customer, 'advance_notice', $html, 'advance_notice_' . $payment->id);
    }

    public function generateUnpaidNotice(Payment $payment): Document
    {
        $customer = $payment->customer;
        $merchant = $payment->merchant;
        $html = view('documents.unpaid_notice', [
            'payment' => $payment,
            'customer' => $customer,
            'merchant' => $merchant,
        ])->render();

        return $this->storeDocument($merchant, $customer, 'unpaid_notice', $html, 'unpaid_notice_' . $payment->id);
    }

    public function generateRefundNotice(RefundRequest $refund): Document
    {
        $customer = $refund->customer;
        $merchant = $refund->merchant;
        $html = view('documents.refund_notice', [
            'refund' => $refund,
            'customer' => $customer,
            'merchant' => $merchant,
        ])->render();

        return $this->storeDocument($merchant, $customer, 'refund_notice', $html, 'refund_' . $refund->id);
    }

    private function storeDocument(Merchant $merchant, Customer $customer, string $type, string $html, string $name): Document
    {
        $output = $this->renderPdf($html);
        $hash = hash('sha256', $output);
        $path = 'documents/' . $merchant->id . '/' . $customer->id . '/' . $name . '.pdf';
        Storage::disk('local')->put($path, $output);

        return Document::create([
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'type' => $type,
            'file_path' => $path,
            'sha256_hash' => $hash,
        ]);
    }

    private function renderPdf(string $html): string
    {
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->output();
        }

        if (class_exists(\Dompdf\Dompdf::class)) {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->render();
            return $dompdf->output();
        }

        return $html;
    }
}
