<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            padding: 32px;
            font-family: Arial, sans-serif;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .invoice-header h1 {
            font-size: 24px;
            font-weight: bold;
        }
        .invoice-header p {
            text-align: right;
            font-size: 14px;
        }
        .receiver-info {
            margin-bottom: 20px;
        }
        .receiver-info p {
            margin: 2px 0;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid black;
        }
        .invoice-table th,
        .invoice-table td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        .payment-confirmation {
            margin-top: 20px;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            gap: 40px;
            align-items: center;
            width: 100%;
        }
        .signature-section span {
            /*border-top: 1px solid black;*/
            text-align: center;
            padding-right: 60px;
        }
        .receiver-signature {
            text-align: right;
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
        <th>Sl No</th>
        <th>Particulars</th>
        <th>No</th>
        <th>Amount</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>1</td>
        <td>{{ $itemDescription }}</td>
        <td>{{ $itemNo }}</td>
        <td>{{ number_format($returnAmount, 2) }}</td>
    </tr>
    </tbody>
</table>

<!-- Payment Confirmation -->

<div class="payment-confirmation">
    <p>Received payment in full _______________</p>
    <strong>Amount in Words:</strong> {{ ucwords(\NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format($returnAmount)) }} Only

</div>

<!-- Signature Section -->
<div class="signature-section">
    <!-- Signature -->
    <span>Sales Promoter</span>
    <span>System Operator</span>
    <span>Manager</span>
</div>

    <!-- Right Section: Receiver Signature -->
    <div class="receiver-signature">
        <p>Signature: __________________</p>
        <p>FROM: {{ $receiverName }}</p>
    </div>


</body>
</html>
