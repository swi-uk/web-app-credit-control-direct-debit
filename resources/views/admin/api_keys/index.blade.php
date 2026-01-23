<x-layout.app title="API Keys">
    <x-ui.card>
        <div class="text-body">Site: {{ $site->site_id }}</div>
        @if (!empty($rawKey))
            <x-ui.alert type="success">
                <strong>New API key:</strong> <code>{{ $rawKey }}</code>
            </x-ui.alert>
        @endif
        <x-ui.button variant="primary" type="button" onclick="location.href='{{ route('admin.api_keys.create', $site) }}'">
            Generate new key
        </x-ui.button>
    </x-ui.card>

    <x-ui.table>
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
                    <td><x-ui.badge :status="$key->status" /></td>
                    <td>{{ $key->last_used_at }}</td>
                    <td>
                        @if ($key->status === 'active')
                            <form method="POST" action="{{ route('admin.api_keys.revoke', [$site, $key]) }}">
                                @csrf
                                <x-ui.button variant="danger" type="submit">Revoke</x-ui.button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </x-ui.table>
</x-layout.app>
