<x-emails.layout :merchant="$merchant" title="Invoice #{{ $invoice->id }}">
    <p>Hello {{ $merchant->name }},</p>
    <p>Your invoice is ready for the period {{ $invoice->period_start }} to {{ $invoice->period_end }}.</p>
    <p>Total: {{ $invoice->total }}</p>
</x-emails.layout>
