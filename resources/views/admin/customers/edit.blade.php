<x-layout.app title="Customer">
    <x-ui.card>
        <div class="text-h3">{{ $customer->email }}</div>
        <div class="text-small">Status: <x-ui.badge :status="$customer->status" /></div>
        <div class="text-small">Tier: {{ $customer->creditProfile?->creditTier?->name ?? 'None' }}</div>
        <div class="text-small">Exposure: {{ $customer->creditProfile?->current_exposure_amount }} / {{ $customer->creditProfile?->limit_amount }}</div>
        <div class="text-small">Account age: {{ $accountAgeDays }} days</div>
    </x-ui.card>

    @if ($errors->any())
        <x-ui.alert type="danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-ui.alert>
    @endif

    <x-ui.card>
        <form method="POST" action="{{ route('admin.customers.update', $customer) }}">
            @csrf
            <x-ui.select name="credit_tier_id" label="Tier">
                <option value="">-- Select tier --</option>
                @foreach ($tiers as $tier)
                    <option value="{{ $tier->id }}" @selected($customer->creditProfile?->credit_tier_id === $tier->id)>
                        {{ $tier->name }} ({{ $tier->max_exposure_amount }} / {{ $tier->max_days }}d)
                    </option>
                @endforeach
            </x-ui.select>
            <div class="form-field">
                <label class="form-label">
                    <input type="checkbox" name="manual_tier_override" value="1" @checked($customer->creditProfile?->manual_tier_override)>
                    Manual tier override
                </label>
            </div>

            <x-ui.select name="status" label="Status">
                @foreach (['active', 'restricted', 'locked', 'blocked'] as $status)
                    <option value="{{ $status }}" @selected($customer->status === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </x-ui.select>

            <div class="form-field">
                <label class="form-label">
                    <input type="checkbox" name="manual_limit_override" value="1" @checked($customer->creditProfile?->manual_limit_override)>
                    Manual limit override
                </label>
            </div>
            <x-ui.input name="limit_amount" label="Credit limit" :value="$customer->creditProfile?->limit_amount" />

            <div class="form-field">
                <label class="form-label">
                    <input type="checkbox" name="manual_days_override" value="1" @checked($customer->creditProfile?->manual_days_override)>
                    Manual days override
                </label>
            </div>
            <x-ui.input name="days_max" label="Days max" :value="$customer->creditProfile?->days_max" />
            <x-ui.input name="days_default" label="Days default" :value="$customer->creditProfile?->days_default" />
            <x-ui.input name="lock_reason" label="Lock reason" :value="$customer->lock_reason" />

            <x-ui.button variant="primary">Save</x-ui.button>
        </form>
    </x-ui.card>

    <x-ui.card>
        <div class="text-h3">External Links</div>
        @if ($externalLinks->isEmpty())
            <x-ui.empty-state>No external links for this customer.</x-ui.empty-state>
        @else
            <x-ui.table>
                <thead>
                    <tr>
                        <th>Site</th>
                        <th>Platform</th>
                        <th>External ID</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($externalLinks as $link)
                        <tr>
                            <td>{{ $link->merchantSite?->site_id }}</td>
                            <td>{{ $link->merchantSite?->platform }}</td>
                            <td>{{ $link->external_id }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </x-ui.table>
        @endif
    </x-ui.card>
</x-layout.app>
