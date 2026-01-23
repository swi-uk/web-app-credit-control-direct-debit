<x-layout.app title="Operations">
    <x-ui.card>
        <div class="text-h3">Webhook failures</div>
        <x-ui.table>
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
        </x-ui.table>
    </x-ui.card>

    <x-ui.card>
        <div class="text-h3">Bureau submission failures</div>
        <x-ui.table>
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
        </x-ui.table>
    </x-ui.card>

    <x-ui.card>
        <div class="text-h3">Report ingestion failures</div>
        <x-ui.table>
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
        </x-ui.table>
    </x-ui.card>

    <x-ui.card>
        <div class="text-h3">Failed jobs</div>
        <x-ui.table>
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
        </x-ui.table>
    </x-ui.card>
</x-layout.app>
