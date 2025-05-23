@php
    $totalAmount = $records->sum('selling_price');
    $totalDiscount = $records->sum('discount');
    $totalGstAmount = $records->sum('gst_amount');
    $totalRate = $records->sum('rate');
    $totalQuantity = $records->sum('quantity');
    // $totalAmountWithGst = $records->sum('total_amount_with_gst');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0px;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin: 0;
            font-size: 14px;
        }
        .header h2 {
            margin: 0;
        }
        .header p {
            margin: 5px 0;
        }
        .bold {
            font-weight: bold;
        }
        .invoice-title {
            text-align: center;
            font-size: 16px;
        }
        .billed-to, .shipped-to {
            display: inline-block;
            width: 48%;
            vertical-align: top;
            font-size: 12px;
        }
        .shipped-to {
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #000;
            font-size: 12px;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        .amount-in-words {
            margin-top: 20px;
        }
        .bank-details, .invoice-summary {
            display: inline-block;
            width: 48%;
            vertical-align: top;
            margin-top: 20px;
            font-size: 12px;
        }
        .invoice-summary {
            display: inline-block;
            vertical-align: top;
            text-align: right;
            line-height: 1.1;
            margin-top: 20px;
            font-size: 12px;
        }
        .manager-signature {
            text-align: right;
            margin-top: 40px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Store Details -->
        <div class="header">
            <h2>BAPTIST LITERATURE SERVICE</h2>
            <p>{{$branch->branch_address}}</p>
            <p>PHONE NO / MOB NO: {{$branch->branch_phone}}</p>
            <p>EMAIL ID: {{$branch->branch_email}}</p>
            <p class="bold">GSTIN: 15AAATB3039Q2ZK</p>
            <p class="bold">Branch: {{ $branch->branch_name }}</p>
        </div>

        <!-- Invoice Title -->
        <div class="invoice-title">
            <h2>TAX INVOICE</h2>
        </div>

        <!-- Billed To and Shipped To -->
        <div class="billed-to">
            @php
            $currentYear = date('y');
            $nextYear = date('y', strtotime('+1 year'));
            $financialYear = (date('m') > 3) ? $currentYear . '-' . $nextYear : ($currentYear - 1) . '-' . date('y');
            $userBranch=auth()->user()->branch->branch_name;
            $branchWords = explode(' ', $userBranch);
            if (count($branchWords) > 1) {
                $branchCode = substr($branchWords[0], 0, 3) . substr($branchWords[1], 0, 1);
            } else {
                $branchCode = substr($userBranch, 0, 3);
            }
            @endphp
            <strong>Invoice No: BLS/{{ $financialYear }}/{{$branchCode}}/{{ $memoNumber.auth()->user()->branch_id}}</strong><br>
            <strong>Billed To:</strong><br>
            Name: {{ $request['name'] ?? 'N/A' }}<br>
            Address: {{$request['address'] ?? 'N/A'}}<br>
            Ph. No: {{$request['phone'] ?? 'N/A' }}<br>
            GSTIN: {{$request['gst_number'] ?? 'N/A' }}
        </div>
        <div class="shipped-to">
            <strong>Invoice Date: {{ date('d/m/Y') }}</strong><br>
            <strong>Shipped To:</strong><br>
            Name: {{ $request['name'] ?? 'N/A' }}<br>
            Address: {{$request['address'] ?? 'N/A'}}<br>
            Ph. No: {{$request['phone'] ?? 'N/A' }}<br>
            GSTIN: {{$request['gst_number'] ?? 'N/A' }}
        </div>

        <!-- Table for Invoice Items -->
        <table>
            <thead>
            <tr>
                <th rowspan="2">Sl.No</th>
                <th rowspan="2">Description of Goods</th>
                <th rowspan="2">HSN Code</th>
                <th rowspan="2">Qty</th>
                <th rowspan="2">Rate</th>
                <th rowspan="2">Mrp</th>
                <th rowspan="2">Taxable Amount</th>
                <th colspan="2">GST</th> <!-- GST Column Spanning 2 Sub-columns -->
                <th rowspan="2">Total Amount</th>
            </tr>
            <!-- Second Row of Header -->
            <tr>
                <th>%</th>
                <th>Amt.</th>
            </tr>
            </thead>
            <tbody>
            @foreach($records as $index => $record)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $record->item->item_name }}</td>
                <td>{{$record->item->hsn_number}}</td>
                <td>{{$record->quantity}}</td>
                <td>{{$record->rate}}</td>
                <td>{{$record->mainStock->mrp}}</td>
                <td>{{$record->rate}}</td>
                <td>{{$record->gst_rate}}</td>
                <td>{{$record->gst_amount}}</td>
                <td>{{$record->selling_price}}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3" class="bold">Total</td>
                <td>{{$totalQuantity}}</td>
                <td></td>
                <td></td>
                <td>{{$totalRate}}</td>
                <td></td>
                <td>{{$totalGstAmount}}</td>
                <td>{{$totalAmount}}</td>
            </tr>
            </tbody>
        </table>

        <!-- Amount in Words -->
        <div class="amount-in-words">
            <strong>Amount in Words:</strong> {{ ucwords(\NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format($totalAmount)) }} Only
        </div>

        <!-- Bank Details and Invoice Summary -->
        <div class="bank-details">
            <strong>Bank Details:</strong><br>
            A/C NAME: BCM PRESS AND BOOKROOM<br>
            A/C NO: 97015814436<br>
            BANK NAME: MIZORAM RURAL BANK<br>
            A/C TYPE: CURRENT<br>
            IFSC CODE: SBIN0RRMIGB<br>
            BRANCH: SERKAWN BRANCH
        </div>
        <div class="invoice-summary">
            <strong>Taxable Amount: {{$totalRate}}</strong><br>
            Discount: {{$totalDiscount}}<br>
{{--            IGST %: {{$records[0]->gst_rate}}<br>--}}
            SGST: {{($totalGstAmount)/2}}<br>
            CGST: {{($totalGstAmount)/2}}<br>
            <strong>Invoice Amount: {{$totalAmount}}</strong><br>
            Freight:<br>
            Insurance: <br>
            Packing or Carrying Charge: <br>
            <strong>TOTAL AMOUNT: <u>{{ $totalAmount}}</u></strong>
        </div>

        <!-- Manager Signature -->
        <div class="manager-signature">
            <p>Manager</p>
            <p>Baptist Literature Service</p>
        </div>
    </div>
</body>
</html>
