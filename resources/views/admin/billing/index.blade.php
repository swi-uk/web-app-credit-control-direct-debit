<x-layout.app title="Billing">
    <x-ui.card>
        <div class="text-h3">Plans</div>
        <x-ui.table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Monthly</th>
                    <th>Included debits</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($plans as $plan)
                    <tr>
                        <td>{{ $plan->code }}</td>
                        <td>{{ $plan->monthly_price }}</td>
                        <td>{{ $plan->included_debits }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-ui.table>
    </x-ui.card>

    <x-ui.card>
        <div class="text-h3">Subscriptions</div>
        <x-ui.table>
            <thead>
                <tr>
                    <th>Merchant</th>
                    <th>Plan</th>
                    <th>Status</th>
                    <th>Period</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($subscriptions as $subscription)
                    <tr>
                        <td>{{ $subscription->merchant?->name }}</td>
                        <td>{{ $subscription->plan?->code }}</td>
                        <td><x-ui.badge :status="$subscription->status" /></td>
                        <td>{{ $subscription->current_period_start }} - {{ $subscription->current_period_end }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-ui.table>
    </x-ui.card>

    <x-ui.card>
        <div class="text-h3">Invoices</div>
        <x-ui.table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Merchant</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>PDF</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $invoice)
                    <tr>
                        <td>{{ $invoice->id }}</td>
                        <td>{{ $invoice->merchant?->name }}</td>
                        <td>{{ $invoice->total }}</td>
                        <td>{{ $invoice->status }}</td>
                        <td>{{ $invoice->pdf_path }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-ui.table>
    </x-ui.card>
</x-layout.app>
