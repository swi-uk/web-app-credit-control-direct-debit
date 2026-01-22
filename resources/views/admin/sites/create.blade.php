<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Create Merchant Site</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 720px; margin: 40px auto; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 8px; }
        .notice { background: #f3f4f6; padding: 12px; border-radius: 6px; margin-top: 16px; }
    </style>
</head>
<body>
    <h1>Create Merchant Site</h1>
    <p><a href="{{ route('admin.sites.index') }}">Back to sites</a></p>

    @if (!empty($created))
        <div class="notice">
            <p><strong>Site created.</strong> Copy these values now. They will not be shown again.</p>
            <p>API Key: <code>{{ $apiKey }}</code></p>
            <p>Webhook Secret: <code>{{ $webhookSecret }}</code></p>
        </div>
    @endif

    @if ($errors->any())
        <div class="notice" style="color:#b91c1c;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.sites.store') }}">
        @csrf
        <label for="merchant_name">Merchant name</label>
        <input id="merchant_name" name="merchant_name" type="text" value="{{ old('merchant_name') }}" required>

        <label for="site_id">Site ID</label>
        <input id="site_id" name="site_id" type="text" value="{{ old('site_id') }}" required>

        <label for="base_url">Base URL</label>
        <input id="base_url" name="base_url" type="text" value="{{ old('base_url') }}" required>

        <div style="margin-top: 16px;">
            <button type="submit">Create site</button>
        </div>
    </form>
</body>
</html>
