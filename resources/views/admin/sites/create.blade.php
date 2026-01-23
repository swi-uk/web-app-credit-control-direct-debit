<x-layout.app title="Create Site">
    @if (!empty($created))
        <x-ui.alert type="success">
            <p><strong>Site created.</strong> Copy these values now. They will not be shown again.</p>
            <p>API Key: <code>{{ $apiKey }}</code></p>
            <p>Webhook Secret: <code>{{ $webhookSecret }}</code></p>
        </x-ui.alert>
    @endif

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
        <form method="POST" action="{{ route('admin.sites.store') }}">
            @csrf
            <x-ui.input name="merchant_name" label="Merchant name" />
            <x-ui.input name="site_id" label="Site ID" />
            <x-ui.input name="base_url" label="Base URL" />
            <x-ui.select name="platform" label="Platform">
                @foreach (['woocommerce', 'shopify', 'custom', 'api'] as $platform)
                    <option value="{{ $platform }}" @selected(old('platform', 'woocommerce') === $platform)>
                        {{ ucfirst($platform) }}
                    </option>
                @endforeach
            </x-ui.select>
            <x-ui.button variant="primary">Create site</x-ui.button>
        </form>
    </x-ui.card>
</x-layout.app>
