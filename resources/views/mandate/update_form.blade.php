<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Update Bank Details</title>
    <link rel="stylesheet" href="/css/tokens.css">
</head>
<body>
<div class="ddi-layout">
    <div class="card ddi-card">
        <div class="text-h2">Update Bank Details</div>
        <div class="text-small">Customer: {{ $customer->email }}</div>

        @if ($errors->any())
            <x-ui.alert type="danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        @endif

        <form method="POST" action="{{ route('mandate.update.submit', $token) }}">
            @csrf
            <x-ui.input name="account_holder_name" label="Account holder name" />
            <x-ui.input name="sort_code" label="Sort code" />
            <x-ui.input name="account_number" label="Account number" />
            <x-ui.input name="bank_name" label="Bank name (optional)" />
            <div class="form-field">
                <label class="form-label">
                    <input type="checkbox" name="consent" value="1" required>
                    I confirm the Direct Debit mandate and consent.
                </label>
            </div>
            <x-ui.button variant="primary">Submit update</x-ui.button>
        </form>
    </div>
</div>
</body>
</html>
