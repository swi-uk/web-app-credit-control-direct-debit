<x-layout.app title="Dashboard">
    <div class="stat-grid">
        <x-ui.stat label="Due Today (7d)" :value="$next7" />
        <x-ui.stat label="Expected 30 days" :value="$next30" />
        <x-ui.stat label="Delinquency rate" :value="$delinquencyRate . '%'" />
    </div>

    <x-ui.card>
        <div class="text-h3">ARUDD reasons</div>
        <x-ui.table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($aruddReasons as $row)
                    <tr>
                        <td>{{ $row->code }}</td>
                        <td>{{ $row->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-ui.table>
    </x-ui.card>

    <x-ui.card>
        <div class="text-h3">Tier distribution</div>
        <x-ui.table>
            <thead>
                <tr>
                    <th>Tier ID</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tierDistribution as $row)
                    <tr>
                        <td>{{ $row->credit_tier_id }}</td>
                        <td>{{ $row->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-ui.table>
    </x-ui.card>

    <x-ui.card>
        <div class="text-h3">Top exposed customers</div>
        <x-ui.table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Exposure</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($topExposure as $customer)
                    <tr>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->creditProfile?->current_exposure_amount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-ui.table>
    </x-ui.card>
</x-layout.app>
