<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payments</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 40px auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Payments</h1>
    <p><a href="{{ route('portal.dashboard') }}">Back to dashboard</a></p>

    <table>
        <thead>
            <tr>
                <th>External order</th>
                <th>Amount</th>
                <th>Due date</th>
                <th>Status</th>
                <th>Next retry</th>
                <th>Documents</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $payment)
                <tr>
                    <td>{{ $payment->external_order_id }}</td>
                    <td>{{ $payment->amount }} {{ $payment->currency }}</td>
                    <td>{{ $payment->due_date }}</td>
                    <td>{{ $payment->status }}</td>
                    <td>{{ $payment->next_retry_at }}</td>
                    <td>
                        <a href="{{ route('portal.documents.advance', $payment) }}">Advance notice</a> |
                        <a href="{{ route('portal.documents.unpaid', $payment) }}">Unpaid notice</a>
                    </td>
                    <td>
                        <a href="{{ route('portal.refunds.create', ['payment_id' => $payment->id]) }}">Request refund</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
