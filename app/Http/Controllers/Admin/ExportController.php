<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Customers\Models\Customer;
use App\Domain\Integrations\Models\ExternalLink;
use App\Domain\Payments\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function payments(Request $request): StreamedResponse
    {
        $status = $request->query('status');
        $from = $request->query('from');
        $to = $request->query('to');

        $query = Payment::with(['customer', 'sourceSite'])->orderBy('id');
        if ($status) {
            $query->where('status', $status);
        }
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $payments = $query->get();

        return response()->streamDownload(function () use ($payments) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'payment_id',
                'external_order_id',
                'amount',
                'currency',
                'status',
                'due_date',
                'customer_email',
                'external_customer_id',
                'site_id',
            ]);
            foreach ($payments as $payment) {
                $externalCustomerId = null;
                if ($payment->sourceSite && $payment->customer) {
                    $externalLink = ExternalLink::where('merchant_site_id', $payment->sourceSite->id)
                        ->where('entity_type', 'customer')
                        ->where('entity_id', $payment->customer->id)
                        ->first();
                    $externalCustomerId = $externalLink?->external_id;
                }
                fputcsv($handle, [
                    $payment->id,
                    $payment->external_order_id,
                    $payment->amount,
                    $payment->currency,
                    $payment->status,
                    $payment->due_date,
                    $payment->customer?->email,
                    $externalCustomerId,
                    $payment->sourceSite?->site_id,
                ]);
            }
            fclose($handle);
        }, 'payments.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function customers(Request $request): StreamedResponse
    {
        $status = $request->query('status');
        $query = Customer::with('creditProfile')->orderBy('id');
        if ($status) {
            $query->where('status', $status);
        }

        $customers = $query->get();

        return response()->streamDownload(function () use ($customers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'customer_id',
                'email',
                'status',
                'current_exposure',
                'credit_limit',
                'days_max',
                'external_ids',
            ]);
            foreach ($customers as $customer) {
                $externalIds = ExternalLink::where('entity_type', 'customer')
                    ->where('entity_id', $customer->id)
                    ->get()
                    ->map(function ($link) {
                        return $link->merchant_site_id . ':' . $link->external_id;
                    })
                    ->implode('|');

                fputcsv($handle, [
                    $customer->id,
                    $customer->email,
                    $customer->status,
                    $customer->creditProfile?->current_exposure_amount,
                    $customer->creditProfile?->limit_amount,
                    $customer->creditProfile?->days_max,
                    $externalIds,
                ]);
            }
            fclose($handle);
        }, 'customers.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
