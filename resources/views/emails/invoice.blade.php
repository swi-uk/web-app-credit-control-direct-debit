Hello {{ $merchant->name }},

Your invoice #{{ $invoice->id }} is ready for the period {{ $invoice->period_start }} to {{ $invoice->period_end }}.

Total: {{ $invoice->total }}
