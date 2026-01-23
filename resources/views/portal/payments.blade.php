<x-layout.portal title="Payments">
    <x-ui.table>
        <thead>
            <tr>
                <th>Order</th>
                <th>Amount</th>
                <th>Due date</th>
                <th>Status</th>
                <th>Next retry</th>
                <th>Documents</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $payment)
                <tr>
                    <td>{{ $payment->external_order_id }}</td>
                    <td>{{ $payment->amount }} {{ $payment->currency }}</td>
                    <td>{{ $payment->due_date }}</td>
                    <td><x-ui.badge :status="$payment->status" /></td>
                    <td>{{ $payment->next_retry_at }}</td>
                    <td>
                        <a href="{{ route('portal.documents.advance', $payment) }}">Advance notice</a> |
                        <a href="{{ route('portal.documents.unpaid', $payment) }}">Unpaid notice</a>
                    </td>
                    <td>
                        <a href="{{ route('portal.refunds.create', ['payment_id' => $payment->id]) }}">Request refund</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </x-ui.table>
</x-layout.portal>
