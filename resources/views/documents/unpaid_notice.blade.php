<h1>Unpaid Return Notice</h1>
<p>Document ID: {{ $payment->id }}</p>
<p>Generated: {{ now() }}</p>
<p>Merchant: {{ $merchant->name }}</p>
<p>Customer: {{ $customer->email }}</p>
<table>
    <tr><td>Amount</td><td>{{ $payment->amount }} {{ $payment->currency }}</td></tr>
    <tr><td>Status</td><td>{{ $payment->status }}</td></tr>
    <tr><td>Failure code</td><td>{{ $payment->failure_code }}</td></tr>
    <tr><td>Failure description</td><td>{{ $payment->failure_description }}</td></tr>
</table>
