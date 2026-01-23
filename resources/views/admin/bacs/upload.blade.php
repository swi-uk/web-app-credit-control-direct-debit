<x-layout.app title="Reports">
    @if (!empty($uploaded))
        <x-ui.alert type="success">
            <strong>Report queued.</strong> Report ID: {{ $reportId }}
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
        <form method="POST" action="{{ route('admin.bacs.upload') }}" enctype="multipart/form-data">
            @csrf
            <x-ui.select name="merchant_id" label="Merchant">
                @foreach ($merchants as $merchant)
                    <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                @endforeach
            </x-ui.select>
            <x-ui.select name="type" label="Report type">
                <option value="ARUDD">ARUDD</option>
                <option value="ADDACS">ADDACS</option>
                <option value="AUDDIS">AUDDIS</option>
            </x-ui.select>
            <x-ui.input name="report_file" label="Report file" type="file" />
            <x-ui.button variant="primary">Upload report</x-ui.button>
        </form>
    </x-ui.card>
</x-layout.app>
