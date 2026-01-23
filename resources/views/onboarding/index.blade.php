<x-layout.app title="Onboarding">
    @if (!$merchant)
        <x-ui.empty-state>Provide a merchant_id query param to begin.</x-ui.empty-state>
    @else
        <x-ui.card>
            <div class="text-body">Merchant: {{ $merchant->name }}</div>
        </x-ui.card>
        <x-ui.table>
            <thead>
                <tr>
                    <th>Step</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($steps as $step)
                    <tr>
                        <td>{{ $step->step_key }}</td>
                        <td><x-ui.badge :status="$step->status" /></td>
                        <td>
                            <form method="POST" action="{{ route('onboarding.update', $step) }}">
                                @csrf
                                <input type="hidden" name="status" value="done">
                                <x-ui.button variant="secondary">Mark done</x-ui.button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-ui.table>
    @endif
</x-layout.app>
