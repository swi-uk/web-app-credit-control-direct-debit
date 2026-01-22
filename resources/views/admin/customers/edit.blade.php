<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Edit Customer</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 720px; margin: 40px auto; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input[type="text"], select { width: 100%; padding: 8px; }
        .notice { background: #f3f4f6; padding: 12px; border-radius: 6px; margin-top: 16px; }
    </style>
</head>
<body>
    <h1>Edit Customer</h1>
    <p><a href="{{ route('admin.customers.index') }}">Back to customers</a></p>

    @if ($errors->any())
        <div class="notice" style="color:#b91c1c;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.customers.update', $customer) }}">
        @csrf
        <h3>Credit tier</h3>
        <p>
            Current tier: {{ $customer->creditProfile?->creditTier?->name ?? 'None' }} |
            Effective limit: {{ $effectiveLimit ?? '-' }} |
            Effective max days: {{ $effectiveDays ?? '-' }}
        </p>
        <p>
            Successful collections: {{ $customer->creditProfile?->successful_collections ?? 0 }},
            Bounces (60d): {{ $customer->creditProfile?->bounces_60d ?? 0 }},
            Account age days: {{ $accountAgeDays }}
        </p>

        <label>
            <input type="checkbox" name="manual_tier_override" value="1" @checked($customer->creditProfile?->manual_tier_override)>
            Manual tier override
        </label>

        <label for="credit_tier_id">Tier</label>
        <select id="credit_tier_id" name="credit_tier_id">
            <option value="">-- Select tier --</option>
            @foreach ($tiers as $tier)
                <option value="{{ $tier->id }}" @selected($customer->creditProfile?->credit_tier_id === $tier->id)>
                    {{ $tier->name }} ({{ $tier->max_exposure_amount }} / {{ $tier->max_days }}d)
                </option>
            @endforeach
        </select>

        <label for="status">Status</label>
        <select id="status" name="status">
            @foreach (['active', 'restricted', 'locked', 'blocked'] as $status)
                <option value="{{ $status }}" @selected($customer->status === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>

        <label>
            <input type="checkbox" name="manual_limit_override" value="1" @checked($customer->creditProfile?->manual_limit_override)>
            Manual limit override
        </label>

        <label for="limit_amount">Credit limit</label>
        <input id="limit_amount" name="limit_amount" type="text" value="{{ old('limit_amount', $customer->creditProfile?->limit_amount) }}" required>

        <label>
            <input type="checkbox" name="manual_days_override" value="1" @checked($customer->creditProfile?->manual_days_override)>
            Manual days override
        </label>

        <label for="days_max">Days max</label>
        <input id="days_max" name="days_max" type="text" value="{{ old('days_max', $customer->creditProfile?->days_max) }}" required>

        <label for="days_default">Days default</label>
        <input id="days_default" name="days_default" type="text" value="{{ old('days_default', $customer->creditProfile?->days_default) }}">

        <label for="lock_reason">Lock reason</label>
        <input id="lock_reason" name="lock_reason" type="text" value="{{ old('lock_reason', $customer->lock_reason) }}">

        <div style="margin-top: 16px;">
            <button type="submit">Save</button>
        </div>
    </form>

    <h2 style="margin-top: 32px;">Links</h2>
    @if ($externalLinks->isEmpty())
        <p>No external links for this customer.</p>
    @else
        <table style="width:100%; border-collapse: collapse; margin-top: 12px;">
            <thead>
                <tr>
                    <th style="text-align:left; border-bottom:1px solid #e5e7eb; padding:6px;">Site</th>
                    <th style="text-align:left; border-bottom:1px solid #e5e7eb; padding:6px;">Platform</th>
                    <th style="text-align:left; border-bottom:1px solid #e5e7eb; padding:6px;">External ID</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($externalLinks as $link)
                    <tr>
                        <td style="border-bottom:1px solid #e5e7eb; padding:6px;">
                            {{ $link->merchantSite?->site_id }}
                        </td>
                        <td style="border-bottom:1px solid #e5e7eb; padding:6px;">
                            {{ $link->merchantSite?->platform }}
                        </td>
                        <td style="border-bottom:1px solid #e5e7eb; padding:6px;">
                            {{ $link->external_id }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
