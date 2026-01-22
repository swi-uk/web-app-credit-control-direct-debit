<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Ops Monitoring</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1100px; margin: 40px auto; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 6px; text-align: left; }
    </style>
</head>
<body>
    <h1>Operational Monitoring</h1>

    <h2>Webhook failures</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Site</th>
                <th>Event</th>
                <th>Error</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($failedWebhooks as $hook)
                <tr>
                    <td>{{ $hook->id }}</td>
                    <td>{{ $hook->merchant_site_id }}</td>
                    <td>{{ $hook->event_type }}</td>
                    <td>{{ $hook->last_error }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Bureau submission failures</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Site</th>
                <th>Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($failedBatches as $batch)
                <tr>
                    <td>{{ $batch->id }}</td>
                    <td>{{ $batch->merchant_site_id }}</td>
                    <td>{{ $batch->type }}</td>
                    <td>{{ $batch->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Report ingestion failures</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Merchant</th>
                <th>Type</th>
                <th>Source</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($failedReports as $report)
                <tr>
                    <td>{{ $report->id }}</td>
                    <td>{{ $report->merchant_id }}</td>
                    <td>{{ $report->type }}</td>
                    <td>{{ $report->source }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Failed jobs</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Connection</th>
                <th>Queue</th>
                <th>Failed at</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($failedJobs as $job)
                <tr>
                    <td>{{ $job->id }}</td>
                    <td>{{ $job->connection }}</td>
                    <td>{{ $job->queue }}</td>
                    <td>{{ $job->failed_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
