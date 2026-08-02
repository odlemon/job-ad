<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice['invoice_id'] }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
        h1 { font-size: 22px; margin: 0 0 4px; }
        .muted { color: #6b7280; }
        .box { border: 1px solid #e5e7eb; border-radius: 4px; padding: 16px; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { text-align: left; padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
        .total { font-size: 16px; font-weight: bold; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>Invoice {{ $invoice['invoice_id'] }}</h1>
    <p class="muted">{{ $companyName }}</p>
    <p class="muted">Date: {{ $invoice['date'] }} · Status: {{ $invoice['status'] }}</p>

    <div class="box">
        <p><strong>Description:</strong> {{ $invoice['description'] }}</p>
        <p><strong>Payment Method:</strong> {{ $invoice['payment_method'] }}</p>
        <table>
            <tr>
                <th>Item</th>
                <th class="right">Amount</th>
            </tr>
            <tr>
                <td>Subtotal</td>
                <td class="right">{{ $invoice['currency'] }} {{ number_format($invoice['amount'], 2) }}</td>
            </tr>
            <tr>
                <td>Tax</td>
                <td class="right">{{ $invoice['currency'] }} {{ number_format($invoice['tax'], 2) }}</td>
            </tr>
            <tr>
                <td class="total">Total</td>
                <td class="right total">{{ $invoice['currency'] }} {{ number_format($invoice['total'], 2) }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
