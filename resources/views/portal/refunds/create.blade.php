<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Request Refund</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 720px; margin: 40px auto; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 8px; }
    </style>
</head>
<body>
    <h1>Request a Refund</h1>
    <p><a href="{{ route('portal.payments') }}">Back to payments</a></p>

    @if ($errors->any())
        <div style="color:#b91c1c;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('portal.refunds.store') }}">
        @csrf
        <input type="hidden" name="payment_id" value="{{ $payment?->id }}">

        <label for="amount_requested">Amount</label>
        <input id="amount_requested" name="amount_requested" type="text" value="{{ old('amount_requested', $payment?->amount) }}">

        <label for="reason">Reason</label>
        <textarea id="reason" name="reason" rows="4" required>{{ old('reason') }}</textarea>

        <div style="margin-top: 16px;">
            <button type="submit">Submit request</button>
        </div>
    </form>
</body>
</html>
