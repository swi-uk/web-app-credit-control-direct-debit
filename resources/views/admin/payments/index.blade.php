<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payments</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1100px; margin: 40px auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Payments</h1>
    <p>
        <a href="{{ route('admin.payments.index') }}">All</a> |
        <a href="{{ route('admin.payments.index', ['filter' => 'today']) }}">Due today</a> |
        <a href="{{ route('admin.payments.index', ['filter' => 'bounced']) }}">Unpaid returns</a>
    </p>

    <table>
        <thead>
            <tr>
                <th>External order</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Due date</th>
                <th>Status</th>
                <th>Site</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $payment)
                <tr>
                    <td>{{ $payment->external_order_id }}</td>
                    <td>{{ $payment->customer?->email }}</td>
                    <td>{{ $payment->amount }} {{ $payment->currency }}</td>
                    <td>{{ $payment->due_date }}</td>
                    <td>{{ $payment->status }}</td>
                    <td>{{ $payment->sourceSite?->site_id }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
