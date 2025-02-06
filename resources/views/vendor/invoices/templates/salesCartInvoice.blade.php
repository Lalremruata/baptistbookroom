@php
    $totalAmount = $records->sum('selling_price');
    $totalDiscount = $records->sum('discount');
    $totalAmountWithGst = $records->sum('total_amount_with_gst');
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
            padding: 20px;
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
        }
        .header h2 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
        }
        .bold {
            font-weight: bold;
        }
        .invoice-title {
            text-align: center;
            font-size: 20px;
        }
        .billed-to, .shipped-to {
            display: inline-block;
            width: 48%;
            vertical-align: top;
        }
        .shipped-to {
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #000;
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
            width: 100%;
            vertical-align: top;
            margin-top: 20px;
        }
        .invoice-summary {
            vertical-align: top;
            text-align: left; /* Align text to the left */
            float: right; /* Move the section to the right */
            width: 30%; /* Ensure proper width */
            margin-top: 20px;
            line-height: 1.2;s
        }
        .manager-signature {
            text-align: right;
            margin-top: 100px;
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
                <th>Sl.No</th>
                <th>Description of Goods</th>
                <th>HSN Code</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>Taxable Amount</th>
                <th>GST%</th>
                <th>GST</th>
                <th>Total Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach($records as $index => $record)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $record->item->item_name }}</td>
                <td>{{$record->item->hsn_number}}</td>
                <td>{{$record->quantity}}</td>
                <td>{{$record->mainStock->mrp}}</td>
                <td>{{$record->selling_price}}</td>
                <td>{{$record->gst_rate}}</td>
                <td>{{$record->gst_amount}}</td>
                <td>{{$record->total_amount_with_gst}}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="5" class="bold">Total</td>
                <td>{{$totalAmount}}</td>
                <td></td>
                <td></td>
                <td>{{$totalAmountWithGst}}</td>
            </tr>
            </tbody>
        </table>

        <!-- Amount in Words -->
        <div class="amount-in-words">
            <strong>Amount in Words:</strong> {{ ucwords(\NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format($totalAmountWithGst)) }} Only
        </div>

        <!-- Bank Details and Invoice Summary -->
        <div class="bank-details">
            <strong>Bank Details:</strong><br>
            A/C NAME: BCM PRESS AND BOOKROOM<br>
            A/C NO: 97015814436<br>
            BANK NAME: MIZORAM RURAL BANK<br>
            A/C TYPE: CURRENT<br>
            IFSC CODE: SBIN0RMIGB<br>
            BRANCH: SERKAWN BRANCH
        </div>
        <div class="invoice-summary">
            <strong>Taxable Amount: {{$totalAmount}}</strong><br>
            Discount: {{$totalDiscount}}<br>
            IGST %: {{$records[0]->gst_rate}}<br>
            SGST %: {{($records[0]->gst_rate)/2}}<br>
            CGST %: {{($records[0]->gst_rate)/2}}<br>
            <strong>Invoice Amount: {{$totalAmountWithGst}}</strong><br>
            Freight:<br>
            Insurance: <br>
            Packing or Carrying Charge: <br>
            <strong>TOTAL AMOUNT: <u>{{ $totalAmountWithGst }}</u></strong>
        </div>

        <!-- Manager Signature -->
        <div class="manager-signature">
            <p>Manager</p>
            <p>Baptist Literature Service</p>
        </div>
    </div>
</body>
</html>