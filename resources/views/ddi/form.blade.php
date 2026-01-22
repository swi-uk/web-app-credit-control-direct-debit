<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Direct Debit Instruction</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 720px; margin: 40px auto; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 8px; }
        .error { color: #b91c1c; }
        .summary { background: #f3f4f6; padding: 12px; border-radius: 6px; }
    </style>
</head>
<body>
    <h1>Direct Debit Instruction</h1>
    <div class="summary">
        <div>Merchant: {{ $merchantName }}</div>
        <div>Amount: {{ $amount }} {{ $currency }}</div>
    </div>

    @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ url('/ddi/' . $token) }}">
        @csrf
        <label for="account_holder_name">Account holder name</label>
        <input id="account_holder_name" name="account_holder_name" type="text" value="{{ old('account_holder_name') }}" required>

        <label for="sort_code">Sort code</label>
        <input id="sort_code" name="sort_code" type="text" value="{{ old('sort_code') }}" required>

        <label for="account_number">Account number</label>
        <input id="account_number" name="account_number" type="text" value="{{ old('account_number') }}" required>

        <label for="bank_name">Bank name (optional)</label>
        <input id="bank_name" name="bank_name" type="text" value="{{ old('bank_name') }}">

        <label>
            <input type="checkbox" name="consent" value="1" required>
            I confirm the Direct Debit mandate and consent.
        </label>

        <div style="margin-top: 16px;">
            <button type="submit">Submit mandate</button>
        </div>
    </form>
</body>
</html>
