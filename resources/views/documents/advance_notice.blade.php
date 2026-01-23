<h1>Advance Notice</h1>
<p>Document ID: {{ $payment->id }}</p>
<p>Generated: {{ now() }}</p>
<p>Merchant: {{ $merchant->name }}</p>
<p>Customer: {{ $customer->email }}</p>
<table>
    <tr><td>Amount</td><td>{{ $payment->amount }} {{ $payment->currency }}</td></tr>
    <tr><td>Due date</td><td>{{ $payment->due_date }}</td></tr>
</table>
