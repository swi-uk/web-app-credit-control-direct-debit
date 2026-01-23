<x-layout.portal title="Direct Debit">
    <x-ui.table>
        <thead>
            <tr>
                <th>Reference</th>
                <th>Status</th>
                <th>Created</th>
                <th>Document</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mandates as $mandate)
                <tr>
                    <td>{{ $mandate->reference }}</td>
                    <td><x-ui.badge :status="$mandate->status" /></td>
                    <td>{{ $mandate->created_at }}</td>
                    <td><a href="{{ route('portal.documents.mandate', $mandate) }}">Receipt</a></td>
                </tr>
            @endforeach
        </tbody>
    </x-ui.table>
</x-layout.portal>
