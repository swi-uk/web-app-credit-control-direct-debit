Hello {{ $customer->first_name ?? $customer->email }},

This is a friendly advance notice that a Direct Debit payment of {{ $payment->amount }} {{ $payment->currency }}
is scheduled for {{ $payment->due_date }}.

If you have any questions, please contact {{ $merchant->name }}.

Thank you.
