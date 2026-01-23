<x-layout.app title="Refunds & Disputes">
    <x-ui.card>
        <div class="text-body">
            <a href="{{ route('admin.refunds.index') }}">All</a> |
            <a href="{{ route('admin.refunds.index', ['status' => 'requested']) }}">Requested</a> |
            <a href="{{ route('admin.refunds.index', ['status' => 'approved']) }}">Approved</a> |
            <a href="{{ route('admin.refunds.index', ['status' => 'denied']) }}">Denied</a> |
            <a href="{{ route('admin.refunds.index', ['status' => 'processed']) }}">Processed</a>
        </div>
    </x-ui.card>

    <x-ui.table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Payment</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Reason</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($refunds as $refund)
                <tr>
                    <td>{{ $refund->id }}</td>
                    <td>{{ $refund->customer?->email }}</td>
                    <td>{{ $refund->payment?->external_order_id }}</td>
                    <td>{{ $refund->amount_requested }}</td>
                    <td><x-ui.badge :status="$refund->status" /></td>
                    <td>{{ $refund->reason }}</td>
                    <td>
                        @if ($refund->status === 'requested')
                            <form method="POST" action="{{ route('admin.refunds.approve', $refund) }}">
                                @csrf
                                <x-ui.button variant="primary" type="submit">Approve</x-ui.button>
                            </form>
                            <form method="POST" action="{{ route('admin.refunds.deny', $refund) }}">
                                @csrf
                                <input type="hidden" name="admin_note" value="Denied">
                                <x-ui.button variant="danger" type="submit">Deny</x-ui.button>
                            </form>
                        @elseif ($refund->status === 'approved')
                            <form method="POST" action="{{ route('admin.refunds.processed', $refund) }}">
                                @csrf
                                <x-ui.button variant="secondary" type="submit">Mark processed</x-ui.button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </x-ui.table>
</x-layout.app>
