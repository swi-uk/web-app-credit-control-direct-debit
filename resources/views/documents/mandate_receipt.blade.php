<h1>Mandate Receipt</h1>
<p>Document ID: {{ $mandate->id }}</p>
<p>Generated: {{ now() }}</p>
<p>Merchant: {{ $merchant->name }}</p>
<p>Customer: {{ $customer->email }}</p>
<table>
    <tr><td>Reference</td><td>{{ $mandate->reference }}</td></tr>
    <tr><td>Status</td><td>{{ $mandate->status }}</td></tr>
    <tr><td>Created</td><td>{{ $mandate->created_at }}</td></tr>
</table>
