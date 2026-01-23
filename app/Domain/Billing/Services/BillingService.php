<?php

namespace App\Domain\Billing\Services;

use App\Domain\Billing\Models\Invoice;
use App\Domain\Billing\Models\InvoiceLineItem;
use App\Domain\Billing\Models\MerchantContact;
use App\Domain\Billing\Models\MerchantSubscription;
use App\Domain\Billing\Models\UsageRecord;
use App\Domain\Partners\Models\PartnerCommission;
use App\Domain\Partners\Models\PartnerMerchant;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class BillingService
{
    public function generateInvoice(MerchantSubscription $subscription): Invoice
    {
        $merchant = $subscription->merchant;
        $plan = $subscription->plan;

        $periodStart = $subscription->current_period_start;
        $periodEnd = $subscription->current_period_end;

        $usage = UsageRecord::where('merchant_id', $merchant->id)
            ->where('period_start', '>=', $periodStart)
            ->where('period_end', '<=', $periodEnd)
            ->get()
            ->groupBy('metric');

        $mandates = $usage->get('mandates_active')?->sum('quantity') ?? 0;
        $debits = $usage->get('debits_success')?->sum('quantity') ?? 0;
        $sms = $usage->get('sms_sent')?->sum('quantity') ?? 0;

        $lineItems = [];

        $lineItems[] = [
            'description' => 'Monthly plan (' . $plan->code . ')',
            'metric' => null,
            'unit_price' => $plan->monthly_price,
            'quantity' => 1,
            'line_total' => $plan->monthly_price,
        ];

        $lineItems = array_merge($lineItems, $this->overageLine('Mandate overage', 'mandates_active', $mandates, $plan->included_mandates, $plan->per_mandate_fee));
        $lineItems = array_merge($lineItems, $this->overageLine('Debit overage', 'debits_success', $debits, $plan->included_debits, $plan->per_debit_fee));
        $lineItems = array_merge($lineItems, $this->overageLine('SMS overage', 'sms_sent', $sms, $plan->included_sms, $plan->per_sms_fee));

        $subtotal = array_sum(array_column($lineItems, 'line_total'));
        $tax = 0;
        $total = $subtotal + $tax;

        $invoice = Invoice::create([
            'merchant_id' => $merchant->id,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'status' => 'issued',
        ]);

        foreach ($lineItems as $line) {
            InvoiceLineItem::create(array_merge($line, ['invoice_id' => $invoice->id]));
        }

        $pdfPath = $this->generatePdf($invoice);
        $invoice->pdf_path = $pdfPath;
        $invoice->save();

        $this->sendInvoiceEmail($invoice);
        $this->calculatePartnerCommission($invoice);

        return $invoice;
    }

    private function overageLine(string $description, string $metric, int $usage, int $included, float $unitPrice): array
    {
        $overage = max(0, $usage - $included);
        if ($overage === 0 || $unitPrice <= 0) {
            return [];
        }

        return [[
            'description' => $description,
            'metric' => $metric,
            'unit_price' => $unitPrice,
            'quantity' => $overage,
            'line_total' => $overage * $unitPrice,
        ]];
    }

    private function generatePdf(Invoice $invoice): string
    {
        $invoice->load('merchant', 'lineItems');
        $html = view('documents.invoice', [
            'invoice' => $invoice,
            'merchant' => $invoice->merchant,
            'lines' => $invoice->lineItems,
        ])->render();

        $output = $this->renderPdf($html);
        $path = 'invoices/' . $invoice->merchant_id . '/invoice_' . $invoice->id . '.pdf';
        Storage::disk('local')->put($path, $output);

        return $path;
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

    private function sendInvoiceEmail(Invoice $invoice): void
    {
        $merchant = $invoice->merchant;
        $contact = MerchantContact::where('merchant_id', $merchant->id)
            ->where('role', 'billing')
            ->orderByDesc('is_primary')
            ->first();

        if (!$contact) {
            return;
        }

        Mail::send('emails.invoice', [
            'invoice' => $invoice,
            'merchant' => $merchant,
        ], function ($message) use ($contact) {
            $message->to($contact->email)->subject('Your invoice');
        });
    }

    private function calculatePartnerCommission(Invoice $invoice): void
    {
        $partnerLink = PartnerMerchant::where('merchant_id', $invoice->merchant_id)->first();
        if (!$partnerLink) {
            return;
        }

        $amount = $partnerLink->commission_type === 'percentage'
            ? ($invoice->subtotal * ((float) $partnerLink->commission_value / 100))
            : (float) $partnerLink->commission_value;

        PartnerCommission::create([
            'partner_id' => $partnerLink->partner_id,
            'merchant_id' => $invoice->merchant_id,
            'period_start' => $invoice->period_start,
            'period_end' => $invoice->period_end,
            'basis' => 'volume',
            'amount' => $amount,
            'status' => 'calculated',
        ]);
    }
}
