<x-layout.app title="Integrations">
    <x-ui.card>
        <x-ui.button variant="primary" type="button" onclick="location.href='{{ route('admin.sites.create') }}'">
            Create new site
        </x-ui.button>
    </x-ui.card>

    <x-ui.table>
        <thead>
            <tr>
                <th>Merchant</th>
                <th>Site ID</th>
                <th>Platform</th>
                <th>Mode</th>
                <th>Base URL</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sites as $site)
                <tr>
                    <td>{{ $site->merchant?->name }}</td>
                    <td>{{ $site->site_id }}</td>
                    <td>{{ $site->platform }}</td>
                    <td>{{ $site->mode ?? 'test' }}</td>
                    <td>{{ $site->base_url }}</td>
                    <td><a href="{{ route('admin.api_keys.index', $site) }}">API keys</a></td>
                </tr>
            @endforeach
        </tbody>
    </x-ui.table>
</x-layout.app>
