<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Portfolio Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1100px; margin: 40px auto; }
        .card { background: #f3f4f6; padding: 12px; border-radius: 6px; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 6px; text-align: left; }
    </style>
</head>
<body>
    <h1>Portfolio Dashboard</h1>

    <div class="card">
        <strong>Expected collections next 7 days:</strong> {{ $next7 }}<br>
        <strong>Expected collections next 30 days:</strong> {{ $next30 }}<br>
        <strong>Delinquency rate:</strong> {{ $delinquencyRate }}%
    </div>

    <h2>ARUDD reasons</h2>
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($aruddReasons as $row)
                <tr>
                    <td>{{ $row->code }}</td>
                    <td>{{ $row->total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Tier distribution</h2>
    <table>
        <thead>
            <tr>
                <th>Tier ID</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tierDistribution as $row)
                <tr>
                    <td>{{ $row->credit_tier_id }}</td>
                    <td>{{ $row->total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Top exposed customers</h2>
    <table>
        <thead>
            <tr>
                <th>Customer</th>
                <th>Exposure</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($topExposure as $customer)
                <tr>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->creditProfile?->current_exposure_amount }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
