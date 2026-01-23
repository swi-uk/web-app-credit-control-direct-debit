<x-layout.app title="Edit Credit Tier">
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
        <form method="POST" action="{{ route('admin.credit_tiers.update', $tier) }}">
            @csrf
            <x-ui.select name="merchant_id" label="Merchant">
                @foreach ($merchants as $merchant)
                    <option value="{{ $merchant->id }}" @selected($tier->merchant_id === $merchant->id)>
                        {{ $merchant->name }}
                    </option>
                @endforeach
            </x-ui.select>
            <x-ui.input name="name" label="Name" :value="$tier->name" />
            <x-ui.input name="max_exposure_amount" label="Max exposure amount" :value="$tier->max_exposure_amount" />
            <x-ui.input name="max_days" label="Max days" :value="$tier->max_days" />
            <x-ui.input name="priority" label="Priority" :value="$tier->priority" />

            <div class="form-field">
                <label class="form-label">
                    <input type="checkbox" name="is_default" value="1" @checked($tier->is_default)>
                    Default tier
                </label>
            </div>
            <div class="form-field">
                <label class="form-label">
                    <input type="checkbox" name="is_active" value="1" @checked($tier->is_active)>
                    Active
                </label>
            </div>

            <div class="text-h3">Eligibility Rules</div>
            <x-ui.input name="min_successful_collections" label="Min successful collections" :value="$rule?->min_successful_collections ?? 0" />
            <x-ui.input name="max_bounces_60d" label="Max bounces (60d)" :value="$rule?->max_bounces_60d ?? 999" />
            <x-ui.input name="min_account_age_days" label="Min account age days" :value="$rule?->min_account_age_days ?? 0" />

            <x-ui.button variant="primary">Save tier</x-ui.button>
        </form>
    </x-ui.card>
</x-layout.app>
