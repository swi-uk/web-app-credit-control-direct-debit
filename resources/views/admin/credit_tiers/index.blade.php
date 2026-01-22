<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Credit Tiers</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1100px; margin: 40px auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Credit Tiers</h1>
    <p><a href="{{ route('admin.credit_tiers.create') }}">Create tier</a></p>
    <table>
        <thead>
            <tr>
                <th>Merchant</th>
                <th>Name</th>
                <th>Max exposure</th>
                <th>Max days</th>
                <th>Priority</th>
                <th>Default</th>
                <th>Active</th>
                <th>Rules</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tiers as $tier)
                <tr>
                    <td>{{ $tier->merchant?->name }}</td>
                    <td>{{ $tier->name }}</td>
                    <td>{{ $tier->max_exposure_amount }}</td>
                    <td>{{ $tier->max_days }}</td>
                    <td>{{ $tier->priority }}</td>
                    <td>{{ $tier->is_default ? 'Yes' : 'No' }}</td>
                    <td>{{ $tier->is_active ? 'Yes' : 'No' }}</td>
                    <td>
                        @php $rule = $tier->rules->first(); @endphp
                        @if ($rule)
                            Success >= {{ $rule->min_successful_collections }},
                            Bounces <= {{ $rule->max_bounces_60d }},
                            Age >= {{ $rule->min_account_age_days }}
                        @endif
                    </td>
                    <td><a href="{{ route('admin.credit_tiers.edit', $tier) }}">Edit</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
