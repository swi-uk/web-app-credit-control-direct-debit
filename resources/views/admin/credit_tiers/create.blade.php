<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Create Credit Tier</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 720px; margin: 40px auto; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input[type="text"], select { width: 100%; padding: 8px; }
    </style>
</head>
<body>
    <h1>Create Credit Tier</h1>
    <p><a href="{{ route('admin.credit_tiers.index') }}">Back to tiers</a></p>

    @if ($errors->any())
        <div style="color:#b91c1c;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.credit_tiers.store') }}">
        @csrf
        <label for="merchant_id">Merchant</label>
        <select id="merchant_id" name="merchant_id" required>
            @foreach ($merchants as $merchant)
                <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
            @endforeach
        </select>

        <label for="name">Name</label>
        <input id="name" name="name" type="text" required>

        <label for="max_exposure_amount">Max exposure amount</label>
        <input id="max_exposure_amount" name="max_exposure_amount" type="text" required>

        <label for="max_days">Max days</label>
        <input id="max_days" name="max_days" type="text" required>

        <label for="priority">Priority</label>
        <input id="priority" name="priority" type="text" required>

        <label>
            <input type="checkbox" name="is_default" value="1">
            Default tier
        </label>

        <label>
            <input type="checkbox" name="is_active" value="1" checked>
            Active
        </label>

        <h3>Eligibility Rules</h3>
        <label for="min_successful_collections">Min successful collections</label>
        <input id="min_successful_collections" name="min_successful_collections" type="text" value="0">

        <label for="max_bounces_60d">Max bounces (60d)</label>
        <input id="max_bounces_60d" name="max_bounces_60d" type="text" value="999">

        <label for="min_account_age_days">Min account age days</label>
        <input id="min_account_age_days" name="min_account_age_days" type="text" value="0">

        <div style="margin-top: 16px;">
            <button type="submit">Create tier</button>
        </div>
    </form>
</body>
</html>
