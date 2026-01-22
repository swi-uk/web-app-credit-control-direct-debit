<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Customers</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 960px; margin: 40px auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Customers</h1>
    <p>
        <a href="{{ route('admin.customers.index') }}">All</a> |
        <a href="{{ route('admin.customers.index', ['filter' => 'locked']) }}">Locked / Restricted</a>
    </p>
    <table>
        <thead>
            <tr>
                <th>Email</th>
                <th>Status</th>
                <th>Lock reason</th>
                <th>Current exposure</th>
                <th>Credit limit</th>
                <th>Days max</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($customers as $customer)
                <tr>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->status }}</td>
                    <td>{{ $customer->lock_reason }}</td>
                    <td>{{ $customer->creditProfile?->current_exposure_amount }}</td>
                    <td>{{ $customer->creditProfile?->limit_amount }}</td>
                    <td>{{ $customer->creditProfile?->days_max }}</td>
                    <td><a href="{{ route('admin.customers.edit', $customer) }}">Edit</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
