<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>API Keys</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 40px auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        .notice { background: #f3f4f6; padding: 12px; border-radius: 6px; margin-bottom: 12px; }
    </style>
</head>
<body>
    <h1>API Keys</h1>
    <p>Site: {{ $site->site_id }}</p>

    @if (!empty($rawKey))
        <div class="notice">
            <strong>New API key:</strong> <code>{{ $rawKey }}</code>
        </div>
    @endif

    <p>
        <a href="{{ route('admin.api_keys.create', $site) }}">Generate new key</a>
    </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Status</th>
                <th>Last used</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($keys as $key)
                <tr>
                    <td>{{ $key->id }}</td>
                    <td>{{ $key->name }}</td>
                    <td>{{ $key->status }}</td>
                    <td>{{ $key->last_used_at }}</td>
                    <td>
                        @if ($key->status === 'active')
                            <form method="POST" action="{{ route('admin.api_keys.revoke', [$site, $key]) }}">
                                @csrf
                                <button type="submit">Revoke</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
