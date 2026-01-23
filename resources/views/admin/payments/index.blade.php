<x-layout.app title="Payments">
    <x-ui.card>
        <div class="text-body">
            <a href="{{ route('admin.payments.index') }}">All</a> |
            <a href="{{ route('admin.payments.index', ['filter' => 'today']) }}">Due today</a> |
            <a href="{{ route('admin.payments.index', ['filter' => 'week']) }}">Due this week</a> |
            <a href="{{ route('admin.payments.index', ['filter' => 'bounced']) }}">Unpaid returns</a> |
            <a href="{{ route('admin.export.payments', ['status' => 'scheduled']) }}">Export CSV</a>
        </div>
    </x-ui.card>

    <x-ui.table>
        <thead>
            <tr>
                <th>External order</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Due date</th>
                <th>Status</th>
                <th>Site</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $payment)
                <tr>
                    <td>{{ $payment->external_order_id }}</td>
                    <td>{{ $payment->customer?->email }}</td>
                    <td>{{ $payment->amount }} {{ $payment->currency }}</td>
                    <td>{{ $payment->due_date }}</td>
                    <td><x-ui.badge :status="$payment->status" /></td>
                    <td>{{ $payment->sourceSite?->site_id }}</td>
                    <td>
                        @if ($payment->status !== 'collected')
                            <form method="POST" action="{{ route('admin.payments.markCollected', $payment) }}">
                                @csrf
                                <x-ui.button variant="secondary" type="submit">Mark collected</x-ui.button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </x-ui.table>
</x-layout.app>
