<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 1cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 10px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 15px;
        }
        .invoice-header h1 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .invoice-header p {
            text-align: right;
            font-size: 12px;
            margin: 0;
        }
        .receiver-info {
            margin-bottom: 15px;
        }
        .receiver-info p {
            margin: 2px 0;
            font-size: 12px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .invoice-table th,
        .invoice-table td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
            font-size: 11px;
        }
        .invoice-table th {
            background-color: #f2f2f2;
        }
        .payment-confirmation {
            margin-top: 15px;
            font-size: 12px;
        }
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
        .signature-section span {
            display: inline-block;
            width: 30%;
            text-align: center;
            border-top: 1px solid black;
            padding-top: 5px;
            font-size: 11px;
        }
        .receiver-signature {
            margin-top: 20px;
            text-align: right;
            font-size: 12px;
        }
        .amount-in-words {
            margin: 5px 0;
            font-style: italic;
        }
    </style>
</head>
<body>

<!-- Invoice Header -->
<div class="invoice-header">
    <h1>BILL</h1>
    <p>Date: {{ $date }}</p>
</div>

<!-- Receiver Information -->
<div class="receiver-info">
    <p><strong>To,</strong></p>
    <p>The Manager</p>
    <p>Baptist Literature Service, Aizawl</p>
</div>

<!-- Table -->
<table class="invoice-table">
    <thead>
    <tr>
        <th style="width: 10%">Sl No</th>
        <th style="width: 50%">Particulars</th>
        <th style="width: 20%">No</th>
        <th style="width: 20%">Amount</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>1</td>
        <td>{{ $itemDescription }}</td>
        <td>{{ $itemNo }}</td>
        <td>₹ {{ number_format($returnAmount, 2) }}</td>
    </tr>
    <tr>
        <td colspan="3" style="text-align: right;"><strong>Total</strong></td>
        <td><strong>₹ {{ number_format($returnAmount, 2) }}</strong></td>
    </tr>
    </tbody>
</table>

<!-- Payment Confirmation -->
<div class="payment-confirmation">
    <p>Received payment in full _______________</p>
    <div class="amount-in-words">
        <strong>Amount in Words:</strong> {{ ucwords(\NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format($returnAmount)) }} Only
    </div>
</div>

<!-- Signature Section -->
<div class="signature-section">
    <span>Sales Promoter</span>
    <span>System Operator</span>
    <span>Manager</span>
</div>

<!-- Receiver Signature -->
<div class="receiver-signature">
    <p>Signature: __________________</p>
    <p>FROM: {{ $receiverName }}</p>
</div>

</body>
</html>
