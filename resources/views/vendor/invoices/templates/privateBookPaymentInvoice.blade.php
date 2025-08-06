<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 0.5cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            padding: 0;
            margin: 0;
        }
        
        /* Main container */
        .invoice-container {
            border: 2px solid black;
            height: 100%;
            position: relative;
        }
        
        /* Header section */
        .header-section {
            display: table;
            width: 100%;
            border-bottom: 1px solid black;
        }
        
        .company-info {
            display: table-cell;
            width: 60%;
            text-align: center;
            vertical-align: middle;
            padding: 8px;
            border-right: 1px solid black;
        }
        
        .company-info h2 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
        }
        
        .company-info p {
            margin: 1px 0;
            font-size: 10px;
        }
        
        .bank-details {
            display: table-cell;
            width: 40%;
            padding: 5px;
            vertical-align: top;
        }
        
        .bank-details h3 {
            margin: 0 0 3px 0;
            font-size: 11px;
            text-align: center;
            font-weight: bold;
        }
        
        .bank-details p {
            margin: 2px 0;
            font-size: 9px;
        }
        
        .bank-details span {
            font-weight: bold;
            display: inline-block;
            width: 60px;
        }
        
        /* Receipt info section */
        .receipt-info {
            padding: 5px 10px;
            border-bottom: 1px solid black;
        }
        
        .receipt-row {
            display: table;
            width: 100%;
        }
        
        .receipt-left, .receipt-right {
            display: table-cell;
            width: 50%;
            font-size: 10px;
        }
        
        .receipt-right {
            text-align: right;
        }
        
        .billed-to {
            margin-top: 3px;
            font-size: 10px;
        }
        
        /* Table section */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .invoice-table th {
            border: 1px solid black;
            padding: 3px;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            background-color: #f5f5f5;
        }
        
        .invoice-table td {
            border: 1px solid black;
            padding: 2px;
            text-align: center;
            font-size: 9px;
            height: 16px;
        }
        
        .particular-cell {
            text-align: left !important;
            padding-left: 8px !important;
        }
        
        .amount-cell {
            text-align: right !important;
            padding-right: 8px !important;
        }
        
        .total-row td {
            font-weight: bold;
            background-color: #f9f9f9;
            padding: 3px;
        }
        
        /* Empty rows for filling */
        .empty-row td {
            height: 18px;
        }
        
        /* Footer section */
        .footer-section {
            padding: 6px 10px;
        }
        
        .payment-received {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        
        .payment-text {
            display: table-cell;
            width: 30%;
            font-style: italic;
            font-size: 10px;
        }
        
        .payment-line {
            display: table-cell;
            width: 70%;
            border-bottom: 1px solid black;
            position: relative;
            top: -3px;
        }
        
        .rupees-section {
            margin: 5px 0;
            font-size: 10px;
        }
        
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 10px;
        }
        
        .manager-bls {
            display: table-cell;
            width: 40%;
            font-size: 10px;
        }
        
        .signature-right {
            display: table-cell;
            width: 60%;
            text-align: right;
            font-size: 10px;
        }
        
        .signature-box {
            display: inline-block;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="invoice-container">
    <!-- Header Section -->
    <div class="header-section">
        <div class="company-info">
            <h2>{{ $receiverName }}</h2>
            <p>{{ $receiverAddress }}</p>
            <p>Phn No. :{{ $receiverPhone }} Pin Code: {{ $receiverPinCode }}</p>
        </div>
        <div class="bank-details">
            <h3>Bank Account Detail</h3>
            <p><span>Ac No :</span> {{ $account_number ?? '_______________' }}</p>
            <p><span>Ac Holder :</span> {{ $account_holder ?? '_______________' }}</p>
            <p><span>IFSC :</span> {{ $ifsc_code ?? '_______________' }}</p>
            <p><span>Branch :</span> {{ $branch_name ?? '_______________' }}</p>
        </div>
    </div>
    
    <!-- Receipt Info Section -->
    <div class="receipt-info">
        <div class="receipt-row">
            <div class="receipt-left">
                <strong>Receipt no:</strong> {{ $invoiceNumber ?? '____' }}
            </div>
            <div class="receipt-right">
                <strong>Receipt Date :</strong> {{ $date }}
            </div>
        </div>
        <div class="billed-to">
            <strong>Billed To :</strong>
            The Manager, Baptist Literature Service.
        </div>
    </div>
    
    <!-- Table Section -->
    <table class="invoice-table">
        <thead>
            <tr>
                <th style="width: 8%">Sl.<br>No.</th>
                <th style="width: 42%">Particular</th>
                <th style="width: 12%">No.</th>
                <th style="width: 12%">Qty</th>
                <th style="width: 13%">Rate</th>
                <th style="width: 13%">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td class="particular-cell">{{ $itemDescription }}</td>
                <td>{{ $itemNo }}</td>
                <td>1</td>
                <td class="amount-cell">{{ number_format($returnAmount, 2) }}</td>
                <td class="amount-cell">{{ number_format($returnAmount, 2) }}</td>
            </tr>
            
            <!-- Reduced empty rows from 8 to 5 -->
            @for($i = 0; $i < 3; $i++)
            <tr class="empty-row">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            @endfor
            
            <!-- Total row -->
            <tr class="total-row">
                <td colspan="5" style="text-align: right; padding-right: 8px;">Total</td>
                <td class="amount-cell">Rs {{ number_format($returnAmount, 2) }}</td>
            </tr>
        </tbody>
    </table>
    
    <!-- Footer Section -->
    <div class="footer-section">
        <div class="payment-received">
            <span class="payment-text">Received payment in full</span>
            <span class="payment-line">&nbsp;</span>
        </div>
        
        <div class="rupees-section">
            <strong>Rupees :</strong> {{ ucwords(\NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format($returnAmount)) }} Only
        </div>
        
        <div class="signature-section">
            <div class="manager-bls">
                <strong>Manager BLS :</strong> _______________________
            </div>
            <div class="signature-right">
                <div class="signature-box">
                    <strong>Signature :</strong> _______________________<br>
                    {{-- <span style="font-size: 9px;">( {{ $receiverName ?? '' }} )</span> --}}
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>