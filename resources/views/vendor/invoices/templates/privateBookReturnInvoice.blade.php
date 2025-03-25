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
        .signature-section {
             margin-top: 60px;
             display: flex;
             gap: 40px;
             align-items: center;
             width: 100%;
         }
        .signature-section span {
            text-align: center;
            padding-right: 60px;
        }
        .receiver-signature {
            text-align: right;
            margin-top: 20px;
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
        <th>Sl.No</th>
        <th>Particulars</th>
        <th>No</th>
        <th>Qty</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>1</td>
        <td>{{ $itemDescription }}</td>
        <td>{{ $itemNo }}</td>
        <td>{{ $returnAmount }}</td>
    </tr>
    </tbody>
</table>

<!-- Signature Section (Now using CSS Grid) -->
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
