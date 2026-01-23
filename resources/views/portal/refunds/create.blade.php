<x-layout.portal title="Request Refund">
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
        <form method="POST" action="{{ route('portal.refunds.store') }}">
            @csrf
            <input type="hidden" name="payment_id" value="{{ $payment?->id }}">
            <x-ui.input name="amount_requested" label="Amount" :value="$payment?->amount" />
            <x-ui.textarea name="reason" label="Reason" rows="4" />
            <x-ui.button variant="primary">Submit request</x-ui.button>
        </form>
    </x-ui.card>
</x-layout.portal>
