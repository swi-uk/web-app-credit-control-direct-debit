<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Refund Requests</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1100px; margin: 40px auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Refund Requests</h1>
    <p>
        <a href="{{ route('admin.refunds.index') }}">All</a> |
        <a href="{{ route('admin.refunds.index', ['status' => 'requested']) }}">Requested</a> |
        <a href="{{ route('admin.refunds.index', ['status' => 'approved']) }}">Approved</a> |
        <a href="{{ route('admin.refunds.index', ['status' => 'denied']) }}">Denied</a> |
        <a href="{{ route('admin.refunds.index', ['status' => 'processed']) }}">Processed</a>
    </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Payment</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Reason</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($refunds as $refund)
                <tr>
                    <td>{{ $refund->id }}</td>
                    <td>{{ $refund->customer?->email }}</td>
                    <td>{{ $refund->payment?->external_order_id }}</td>
                    <td>{{ $refund->amount_requested }}</td>
                    <td>{{ $refund->status }}</td>
                    <td>{{ $refund->reason }}</td>
                    <td>
                        @if ($refund->status === 'requested')
                            <form method="POST" action="{{ route('admin.refunds.approve', $refund) }}" style="display:inline;">
                                @csrf
                                <button type="submit">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.refunds.deny', $refund) }}" style="display:inline;">
                                @csrf
                                <input type="hidden" name="admin_note" value="Denied">
                                <button type="submit">Deny</button>
                            </form>
                        @elseif ($refund->status === 'approved')
                            <form method="POST" action="{{ route('admin.refunds.processed', $refund) }}" style="display:inline;">
                                @csrf
                                <button type="submit">Mark processed</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
