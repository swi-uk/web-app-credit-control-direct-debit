<x-layout.app title="Customers">
    <x-ui.card>
        <div class="text-body">
            <a href="{{ route('admin.customers.index') }}">All</a> |
            <a href="{{ route('admin.customers.index', ['filter' => 'locked']) }}">Locked / Restricted</a> |
            <a href="{{ route('admin.export.customers') }}">Export CSV</a>
        </div>
    </x-ui.card>

    <x-ui.table>
        <thead>
            <tr>
                <th>Customer</th>
                <th>Status</th>
                <th>Tier</th>
                <th>Exposure / Limit</th>
                <th>Last activity</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($customers as $customer)
                <tr>
                    <td>
                        <div>{{ $customer->email }}</div>
                        <div class="text-small">{{ $customer->first_name }} {{ $customer->last_name }}</div>
                    </td>
                    <td><x-ui.badge :status="$customer->status" /></td>
                    <td>{{ $customer->creditProfile?->creditTier?->name ?? '—' }}</td>
                    <td>{{ $customer->creditProfile?->current_exposure_amount }} / {{ $customer->creditProfile?->limit_amount }}</td>
                    <td>{{ $customer->updated_at }}</td>
                    <td><a href="{{ route('admin.customers.edit', $customer) }}">View</a></td>
                </tr>
            @endforeach
        </tbody>
    </x-ui.table>
</x-layout.app>
