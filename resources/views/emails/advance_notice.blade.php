<x-emails.layout :merchant="$merchant" title="Advance notice">
    <p>Hello {{ $customer->first_name ?? $customer->email }},</p>
    <p>This is a friendly advance notice that a Direct Debit payment of {{ $payment->amount }} {{ $payment->currency }}
        is scheduled for {{ $payment->due_date }}.</p>
    <p>{{ config('copy.support_line') }}</p>
    <p>Thank you.</p>
</x-emails.layout>
