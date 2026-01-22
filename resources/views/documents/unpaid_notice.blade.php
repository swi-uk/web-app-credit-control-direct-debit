<h1>Unpaid Return Notice</h1>
<p>Merchant: {{ $merchant->name }}</p>
<p>Customer: {{ $customer->email }}</p>
<p>Payment amount: {{ $payment->amount }} {{ $payment->currency }}</p>
<p>Status: {{ $payment->status }}</p>
<p>Failure code: {{ $payment->failure_code }}</p>
<p>Failure description: {{ $payment->failure_description }}</p>
