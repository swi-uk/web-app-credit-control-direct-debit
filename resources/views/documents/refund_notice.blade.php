<h1>Refund Notice</h1>
<p>Document ID: {{ $refund->id }}</p>
<p>Generated: {{ now() }}</p>
<p>Customer: {{ $customer->email }}</p>
<table>
    <tr><td>Status</td><td>{{ $refund->status }}</td></tr>
    <tr><td>Amount requested</td><td>{{ $refund->amount_requested }}</td></tr>
    <tr><td>Reason</td><td>{{ $refund->reason }}</td></tr>
</table>
