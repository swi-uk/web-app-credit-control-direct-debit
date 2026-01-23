<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Merchant Onboarding</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 40px auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Onboarding</h1>
    @if (!$merchant)
        <p>Provide a merchant_id query param to begin.</p>
    @else
        <p>Merchant: {{ $merchant->name }}</p>
        <table>
            <thead>
                <tr>
                    <th>Step</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($steps as $step)
                    <tr>
                        <td>{{ $step->step_key }}</td>
                        <td>{{ $step->status }}</td>
                        <td>
                            <form method="POST" action="{{ route('onboarding.update', $step) }}">
                                @csrf
                                <input type="hidden" name="status" value="done">
                                <button type="submit">Mark done</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
