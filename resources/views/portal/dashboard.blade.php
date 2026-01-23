<x-layout.portal title="Overview">
    <x-ui.card>
        <div class="text-body">Credit status: <x-ui.badge :status="$customer->status" /></div>
        <div class="text-body">Current exposure: {{ $profile?->current_exposure_amount }}</div>
        <div class="text-body">Tier: {{ $profile?->creditTier?->name ?? 'N/A' }}</div>
        <div class="text-body">Max credit: {{ $effectiveLimit ?? '-' }}</div>
        <div class="text-body">Max days: {{ $effectiveDays ?? '-' }}</div>
    </x-ui.card>

    <form method="POST" action="{{ route('portal.mandate.update') }}">
        @csrf
        <x-ui.button variant="primary">Update bank details</x-ui.button>
    </form>

    <form method="POST" action="{{ route('portal.logout') }}">
        @csrf
        <x-ui.button variant="secondary">Log out</x-ui.button>
    </form>
</x-layout.portal>
