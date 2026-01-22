<h1>Invoice #{{ $invoice->id }}</h1>
<p>Merchant: {{ $merchant->name }}</p>
<p>Period: {{ $invoice->period_start }} - {{ $invoice->period_end }}</p>

<table style="width:100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="text-align:left; border-bottom:1px solid #ccc;">Description</th>
            <th style="text-align:right; border-bottom:1px solid #ccc;">Qty</th>
            <th style="text-align:right; border-bottom:1px solid #ccc;">Unit</th>
            <th style="text-align:right; border-bottom:1px solid #ccc;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($lines as $line)
            <tr>
                <td>{{ $line->description }}</td>
                <td style="text-align:right;">{{ $line->quantity }}</td>
                <td style="text-align:right;">{{ $line->unit_price }}</td>
                <td style="text-align:right;">{{ $line->line_total }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p>Subtotal: {{ $invoice->subtotal }}</p>
<p>Tax: {{ $invoice->tax }}</p>
<p>Total: {{ $invoice->total }}</p>
