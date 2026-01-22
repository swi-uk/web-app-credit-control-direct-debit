<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Edit Credit Tier</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 720px; margin: 40px auto; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input[type="text"], select { width: 100%; padding: 8px; }
    </style>
</head>
<body>
    <h1>Edit Credit Tier</h1>
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

    <form method="POST" action="{{ route('admin.credit_tiers.update', $tier) }}">
        @csrf
        <label for="merchant_id">Merchant</label>
        <select id="merchant_id" name="merchant_id" required>
            @foreach ($merchants as $merchant)
                <option value="{{ $merchant->id }}" @selected($tier->merchant_id === $merchant->id)>
                    {{ $merchant->name }}
                </option>
            @endforeach
        </select>

        <label for="name">Name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $tier->name) }}" required>

        <label for="max_exposure_amount">Max exposure amount</label>
        <input id="max_exposure_amount" name="max_exposure_amount" type="text" value="{{ old('max_exposure_amount', $tier->max_exposure_amount) }}" required>

        <label for="max_days">Max days</label>
        <input id="max_days" name="max_days" type="text" value="{{ old('max_days', $tier->max_days) }}" required>

        <label for="priority">Priority</label>
        <input id="priority" name="priority" type="text" value="{{ old('priority', $tier->priority) }}" required>

        <label>
            <input type="checkbox" name="is_default" value="1" @checked($tier->is_default)>
            Default tier
        </label>

        <label>
            <input type="checkbox" name="is_active" value="1" @checked($tier->is_active)>
            Active
        </label>

        <h3>Eligibility Rules</h3>
        <label for="min_successful_collections">Min successful collections</label>
        <input id="min_successful_collections" name="min_successful_collections" type="text"
               value="{{ old('min_successful_collections', $rule?->min_successful_collections ?? 0) }}">

        <label for="max_bounces_60d">Max bounces (60d)</label>
        <input id="max_bounces_60d" name="max_bounces_60d" type="text"
               value="{{ old('max_bounces_60d', $rule?->max_bounces_60d ?? 999) }}">

        <label for="min_account_age_days">Min account age days</label>
        <input id="min_account_age_days" name="min_account_age_days" type="text"
               value="{{ old('min_account_age_days', $rule?->min_account_age_days ?? 0) }}">

        <div style="margin-top: 16px;">
            <button type="submit">Save tier</button>
        </div>
    </form>
</body>
</html>
