@php
$totalAmount = $records->sum('total_amount');
$totalDiscount = $records->sum('discount');
$totalGstAmount = $records->sum('gst_amount');
$totalRate = $records->sum('rate');
$totalQuantity = $records->sum('quantity');
$totalTaxableValue = $totalRate - $totalDiscount;

// Financial year and invoice number formatting
$currentYear = date('y');
$nextYear = date('y', strtotime('+1 year'));
$financialYear = (date('m') > 3) ? $currentYear . '-' . $nextYear : ($currentYear - 1) . '-' . date('y');
$userBranch = auth()->user()->branch->branch_name;
$branchWords = explode(' ', $userBranch);
if (count($branchWords) > 1) {
    $branchCode = substr($branchWords[0], 0, 3) . substr($branchWords[1], 0, 1);
} else {
    $branchCode = substr($userBranch, 0, 3);
}
$invoiceNo = 'BLS/' . $financialYear . '/' . $branchCode . '/' . $records[0]->memo;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoice</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            padding: 10px;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #000;
        }
        
        /* Header Section */
        .header-row {
            display: table;
            width: 100%;
            border-bottom: 1px solid #000;
        }
        
        .invoice-no-section {
            display: table-cell;
            width: 30%;
            border-right: 1px solid #000;
            padding: 5px;
            vertical-align: middle;
        }
        
        .invoice-no-section div {
            padding: 5px 0;
            font-size: 10px;
        }
        
        .invoice-no-section div:first-child {
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        
        .tax-invoice-title {
            display: table-cell;
            width: 40%;
            text-align: center;
            vertical-align: middle;
            font-size: 16px;
            font-weight: bold;
            padding: 10px;
        }
        
        .empty-right {
            display: table-cell;
            width: 30%;
        }
        
        /* Details Section */
        .details-section {
            border-bottom: 1px solid #000;
            padding: 5px;
        }
        
        .details-title {
            text-align: center;
            font-weight: bold;
            padding: 3px;
            background-color: #f0f0f0;
            border-bottom: 1px solid #000;
        }
        
        .detail-row {
            display: table;
            width: 100%;
            border-bottom: 1px solid #000;
        }
        
        .detail-label {
            display: table-cell;
            width: 15%;
            padding: 3px 5px;
            border-right: 1px solid #000;
            font-weight: bold;
        }
        
        .detail-value {
            display: table-cell;
            padding: 3px 5px;
        }
        
        /* Billing and Shipping Section */
        .billing-shipping {
            display: table;
            width: 100%;
            border-bottom: 1px solid #000;
        }
        
        .billed-to, .shipping-to {
            display: table-cell;
            width: 50%;
            padding: 5px;
            vertical-align: top;
        }
        
        .shipping-to {
            border-left: 1px solid #000;
        }
        
        .section-title {
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        /* Table Section */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            font-size: 10px;
            background-color: #f0f0f0;
        }
        
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-size: 10px;
        }
        
        .text-left {
            text-align: left !important;
        }
        
        .text-right {
            text-align: right !important;
        }
        
        /* Footer Section */
        .footer-section {
            display: table;
            width: 100%;
        }
        
        .amount-words-bank {
            display: table-cell;
            width: 50%;
            padding: 5px;
            vertical-align: top;
        }
        
        .amount-in-words {
            border-bottom: 1px solid #000;
            padding: 5px;
            min-height: 40px;
        }
        
        .bank-details {
            padding: 5px;
        }
        
        .bank-details-title {
            font-weight: bold;
            text-align: center;
            background-color: #f0f0f0;
            padding: 3px;
            border: 1px solid #000;
            margin-bottom: 3px;
        }
        
        .bank-detail-row {
            display: table;
            width: 100%;
            border: 1px solid #000;
            border-top: none;
        }
        
        .bank-label {
            display: table-cell;
            width: 30%;
            padding: 2px 5px;
            border-right: 1px solid #000;
            font-weight: bold;
            font-size: 9px;
        }
        
        .bank-value {
            display: table-cell;
            padding: 2px 5px;
            font-size: 9px;
        }
        
        .summary-section {
            display: table-cell;
            width: 50%;
            padding: 5px;
            vertical-align: top;
            border-left: 1px solid #000;
        }
        
        .summary-row {
            display: table;
            width: 100%;
            border-bottom: 1px solid #000;
        }
        
        .summary-label {
            display: table-cell;
            width: 70%;
            padding: 3px 5px;
            border-right: 1px solid #000;
        }
        
        .summary-value {
            display: table-cell;
            width: 30%;
            padding: 3px 5px;
            text-align: right;
        }
        
        .summary-row.highlight {
            background-color: #e6f3ff;
            font-weight: bold;
        }
        
        /* Signature Section */
        .signature-section {
            text-align: center;
            padding: 20px 5px 10px;
            border-top: 1px solid #000;
        }
        
        .signature-line {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header Row -->
        <div class="header-row">
            <div class="invoice-no-section">
                <div><strong>Invoice No.</strong> {{ $invoiceNo }}</div>
                <div><strong>Date:</strong> {{ \Carbon\Carbon::parse($records[0]->created_at)->format('d/m/Y') }}</div>
            </div>
            <div class="tax-invoice-title">
                TAX INVOICE
            </div>
            <div class="empty-right">
                <!-- This section can remain empty or be used for other information -->
            </div>
        </div>
        
        <!-- Details Section -->
        <div class="details-section">
            <div class="details-title">DETAILS</div>
            <div class="detail-row">
                <div class="detail-label">Name</div>
                <div class="detail-value">BAPTIST LITERATURE SERVICE</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Address</div>
                <div class="detail-value">{{ $branch->branch_address }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Phone No.</div>
                <div class="detail-value">{{ $branch->branch_phone }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Email ID</div>
                <div class="detail-value">{{ $branch->branch_email }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">GSTIN</div>
                <div class="detail-value">15AAATB3039Q2ZK</div>
            </div>
            <div class="detail-row" style="border-bottom: none;">
                <div class="detail-label">State Code</div>
                <div class="detail-value">15</div>
            </div>
        </div>
        
        <!-- Billing and Shipping Section -->
        <div class="billing-shipping">
            <div class="billed-to">
                <div class="section-title">Billed To</div>
                <div>Name: {{ $data['name'] ?? 'N/A' }}</div>
                <div>Address: {{ $data['address'] ?? 'N/A' }}</div>
                <div>Contact No.: {{ $data['phone'] ?? 'N/A' }}</div>
                <div>GSTIN No.: {{ $data['gst_number'] ?? 'N/A' }}</div>
                <div>State Code: {{ $data['state_code'] ?? 'N/A' }}</div>
            </div>
            <div class="shipping-to">
                <div class="section-title">Shipping To:</div>
                <div>Name: {{ $data['name'] ?? 'N/A' }}</div>
                <div>Address: {{ $data['address'] ?? 'N/A' }}</div>
                <div>Contact No.: {{ $data['phone'] ?? 'N/A' }}</div>
                <div>GSTIN No.: {{ $data['gst_number'] ?? 'N/A' }}</div>
                <div>State Code: {{ $data['state_code'] ?? 'N/A' }}</div>
            </div>
        </div>
        
        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th rowspan="2" style="width: 5%">Sl.<br>No</th>
                    <th rowspan="2" style="width: 30%">Particulars</th>
                    <th rowspan="2" style="width: 8%">HSN<br>Code</th>
                    <th rowspan="2" style="width: 7%">Qty<br>(Piece)</th>
                    <th rowspan="2" style="width: 10%">Rate</th>
                    <th rowspan="2" style="width: 8%">Dis<br>count</th>
                    <th rowspan="2" style="width: 10%">Taxable<br>value</th>
                    <th colspan="2" style="width: 12%">GST</th>
                    <th rowspan="2" style="width: 10%">Amount</th>
                </tr>
                <tr>
                    <th style="width: 6%">%</th>
                    <th style="width: 6%">GST</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $index => $record)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $record->item->item_name }}</td>
                    <td>{{ $record->item->hsn_number }}</td>
                    <td>{{ $record->quantity }}</td>
                    <td class="text-right">{{ number_format($record->rate, 2) }}</td>
                    <td class="text-right">{{ number_format($record->discount, 2) }}</td>
                    <td class="text-right">{{ number_format($record->rate - $record->discount, 2) }}</td>
                    <td>{{ $record->gst_rate }}%</td>
                    <td class="text-right">{{ number_format($record->gst_amount, 2) }}</td>
                    <td class="text-right">{{ number_format($record->total_amount, 2) }}</td>
                </tr>
                @endforeach
                
                <!-- Empty rows for space -->
                @for($i = count($records); $i < 5; $i++)
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                @endfor
                
                <!-- Total row -->
                <tr style="font-weight: bold;">
                    <td colspan="3" class="text-right">Total</td>
                    <td>{{ $totalQuantity }}</td>
                    <td></td>
                    <td class="text-right">{{ number_format($totalDiscount, 2) }}</td>
                    <td class="text-right">{{ number_format($totalTaxableValue, 2) }}</td>
                    <td></td>
                    <td class="text-right">{{ number_format($totalGstAmount, 2) }}</td>
                    <td class="text-right">{{ number_format($totalAmount, 2) }}</td>
                </tr>
            </tbody>
        </table>
        
        <!-- Footer Section -->
        <div class="footer-section">
            <!-- Left side: Amount in words and Bank details -->
            <div class="amount-words-bank">
                <div class="amount-in-words">
                    <strong>Amount in words:</strong><br>
                    {{ ucwords(\NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format($totalAmount)) }} Only
                </div>
                
                <div class="bank-details">
                    <div class="bank-details-title">BANK DETAILS</div>
                    <div class="bank-detail-row">
                        <div class="bank-label">A/C Name</div>
                        <div class="bank-value">BCM PRESS AND BOOKROOM</div>
                    </div>
                    <div class="bank-detail-row" style="border-top: 1px solid #000;">
                        <div class="bank-label">Bank Name</div>
                        <div class="bank-value">MIZORAM RURAL BANK</div>
                    </div>
                    <div class="bank-detail-row" style="border-top: 1px solid #000;">
                        <div class="bank-label">A/C No.</div>
                        <div class="bank-value">97015814436</div>
                    </div>
                    <div class="bank-detail-row" style="border-top: 1px solid #000;">
                        <div class="bank-label">A/C Type</div>
                        <div class="bank-value">CURRENT</div>
                    </div>
                    <div class="bank-detail-row" style="border-top: 1px solid #000;">
                        <div class="bank-label">IFS Code</div>
                        <div class="bank-value">SBIN0RRMIGB</div>
                    </div>
                    <div class="bank-detail-row" style="border-top: 1px solid #000;">
                        <div class="bank-label">Branch</div>
                        <div class="bank-value">SERKAWN BRANCH</div>
                    </div>
                </div>
            </div>
            
            <!-- Right side: Summary -->
            <div class="summary-section">
                <div class="summary-row highlight">
                    <div class="summary-label">Sub Total:</div>
                    <div class="summary-value">{{ number_format($totalTaxableValue, 2) }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Discount:</div>
                    <div class="summary-value">{{ number_format($totalDiscount, 2) }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">IGST:</div>
                    <div class="summary-value">0</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">SGST:</div>
                    <div class="summary-value">{{ number_format($totalGstAmount/2, 2) }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">CGST:</div>
                    <div class="summary-value">{{ number_format($totalGstAmount/2, 2) }}</div>
                </div>
                <div class="summary-row highlight">
                    <div class="summary-label">Total Invoice Amount:</div>
                    <div class="summary-value">{{ number_format($totalAmount, 2) }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Freight:</div>
                    <div class="summary-value"></div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Insurance:</div>
                    <div class="summary-value"></div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Packing & forwarding charges:</div>
                    <div class="summary-value"></div>
                </div>
                <div class="summary-row highlight">
                    <div class="summary-label">Total Amount:</div>
                    <div class="summary-value">{{ number_format($totalAmount, 2) }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Received:</div>
                    <div class="summary-value"></div>
                </div>
                <div class="summary-row" style="border-bottom: none;">
                    <div class="summary-label">Balance:</div>
                    <div class="summary-value"></div>
                </div>
            </div>
        </div>
        
        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-line">
                <strong>Manager</strong><br>
                Baptist Literature Service<br>
                ( _____________________ )
            </div>
        </div>
    </div>
</body>
</html>