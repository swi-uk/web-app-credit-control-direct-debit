<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Mandates</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 40px auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Mandates</h1>
    <p><a href="{{ route('portal.dashboard') }}">Back to dashboard</a></p>

    <table>
        <thead>
            <tr>
                <th>Reference</th>
                <th>Status</th>
                <th>Created</th>
                <th>Document</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mandates as $mandate)
                <tr>
                    <td>{{ $mandate->reference }}</td>
                    <td>{{ $mandate->status }}</td>
                    <td>{{ $mandate->created_at }}</td>
                    <td><a href="{{ route('portal.documents.mandate', $mandate) }}">Receipt</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
