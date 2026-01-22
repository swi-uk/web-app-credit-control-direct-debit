<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Billing</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1100px; margin: 40px auto; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 6px; text-align: left; }
    </style>
</head>
<body>
    <h1>Billing</h1>

    <h2>Plans</h2>
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Monthly</th>
                <th>Included debits</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($plans as $plan)
                <tr>
                    <td>{{ $plan->code }}</td>
                    <td>{{ $plan->monthly_price }}</td>
                    <td>{{ $plan->included_debits }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Subscriptions</h2>
    <table>
        <thead>
            <tr>
                <th>Merchant</th>
                <th>Plan</th>
                <th>Status</th>
                <th>Period</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($subscriptions as $subscription)
                <tr>
                    <td>{{ $subscription->merchant?->name }}</td>
                    <td>{{ $subscription->plan?->code }}</td>
                    <td>{{ $subscription->status }}</td>
                    <td>{{ $subscription->current_period_start }} - {{ $subscription->current_period_end }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Invoices</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Merchant</th>
                <th>Total</th>
                <th>Status</th>
                <th>PDF</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->id }}</td>
                    <td>{{ $invoice->merchant?->name }}</td>
                    <td>{{ $invoice->total }}</td>
                    <td>{{ $invoice->status }}</td>
                    <td>{{ $invoice->pdf_path }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
