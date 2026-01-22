<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Portal Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 40px auto; }
        .card { background: #f3f4f6; padding: 12px; border-radius: 6px; margin-bottom: 12px; }
    </style>
</head>
<body>
    <h1>Welcome</h1>
    <p>
        <a href="{{ route('portal.payments') }}">Payments</a> |
        <a href="{{ route('portal.mandates') }}">Mandates</a>
    </p>

    <div class="card">
        <strong>Credit status:</strong> {{ $customer->status }}<br>
        <strong>Current exposure:</strong> {{ $profile?->current_exposure_amount }}<br>
        <strong>Tier:</strong> {{ $profile?->creditTier?->name ?? 'N/A' }}<br>
        <strong>Max credit:</strong> {{ $effectiveLimit ?? '-' }}<br>
        <strong>Max days:</strong> {{ $effectiveDays ?? '-' }}
    </div>

    <form method="POST" action="{{ route('portal.mandate.update') }}">
        @csrf
        <button type="submit">Update bank details</button>
    </form>

    <form method="POST" action="{{ route('portal.logout') }}" style="margin-top: 12px;">
        @csrf
        <button type="submit">Log out</button>
    </form>
</body>
</html>
