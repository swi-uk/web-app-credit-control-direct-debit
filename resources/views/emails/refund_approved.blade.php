<x-emails.layout :merchant="$customer->merchant" title="Refund approved">
    <p>Hello {{ $customer->first_name ?? $customer->email }},</p>
    <p>Your refund request has been approved. We will process it shortly.</p>
</x-emails.layout>
