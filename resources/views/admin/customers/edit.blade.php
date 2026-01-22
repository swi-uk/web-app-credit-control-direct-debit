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
        <label for="status">Status</label>
        <select id="status" name="status">
            @foreach (['active', 'locked', 'blocked'] as $status)
                <option value="{{ $status }}" @selected($customer->status === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>

        <label for="limit_amount">Credit limit</label>
        <input id="limit_amount" name="limit_amount" type="text" value="{{ old('limit_amount', $customer->creditProfile?->limit_amount) }}" required>

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
</body>
</html>
