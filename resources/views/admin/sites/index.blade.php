<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Merchant Sites</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 960px; margin: 40px auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Merchant Sites</h1>
    <p><a href="{{ route('admin.sites.create') }}">Create new site</a></p>

    <table>
        <thead>
            <tr>
                <th>Merchant</th>
                <th>Site ID</th>
                <th>Platform</th>
                <th>Base URL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sites as $site)
                <tr>
                    <td>{{ $site->merchant?->name }}</td>
                    <td>{{ $site->site_id }}</td>
                    <td>{{ $site->platform }}</td>
                    <td>{{ $site->base_url }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
