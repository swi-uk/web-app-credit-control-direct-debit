<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Secure Direct Debit setup</title>
    <link rel="stylesheet" href="/css/tokens.css">
</head>
<body>
<div class="ddi-layout">
    <div class="card ddi-card">
        <div class="ddi-header">
            <div class="ddi-logo">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $merchantName }}" width="32" height="32">
                @else
                    <span>DD</span>
                @endif
            </div>
            <div>
                <div class="text-h2">{{ $merchantName }}</div>
                @if ($supportEmail)
                    <div class="text-small">Support: {{ $supportEmail }}</div>
                @endif
            </div>
        </div>
        <div class="ddi-trust">
            <span>🔒 Secure Direct Debit setup</span>
        </div>

        <x-ui.card>
            <div class="text-body">Amount: <strong>{{ $amount }} {{ $currency }}</strong></div>
        </x-ui.card>

        @if ($errors->any())
            <x-ui.alert type="danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        @endif

        <form method="POST" action="{{ url('/ddi/' . $token) }}">
            @csrf
            <x-ui.input name="account_holder_name" label="Account holder name" />
            <x-ui.input name="sort_code" label="Sort code" />
            <x-ui.input name="account_number" label="Account number" />
            <x-ui.input name="bank_name" label="Bank name (optional)" />

            <div class="form-field">
                <label class="form-label">
                    <input type="checkbox" name="consent" value="1" required>
                    I confirm this Direct Debit mandate and consent to future collections.
                </label>
            </div>

            <x-ui.button variant="primary">Submit mandate</x-ui.button>
        </form>
    </div>
</div>
</body>
</html>
