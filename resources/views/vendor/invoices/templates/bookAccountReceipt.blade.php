@php
    use Carbon\Carbon;
@endphp

    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .receipt {
            width: 600px;
            border: 2px solid black;
            padding: 20px;
            margin: auto;
        }
        .header {
            display: flex; /* Ensures items are in a row */
            align-items: center; /* Vertically centers the items */
            justify-content: flex-start; /* Aligns items to the start of the row */
            font-weight: bold;
        }
        .header img {
            width: 50px;
            height: auto;
            margin-right: 10px; /* Adds spacing between the logo and text */
        }
        .header-text {
            /*text-align: left; !* Aligns text to the left *!*/
            flex-grow: 1; /* Allows the text to take up remaining space */
        }
        .details {
            margin-top: 10px;
            border-collapse: collapse;
            width: 100%;
        }
        .details td {
            padding: 5px;
        }
        .rules {
            margin-top: 20px;
            font-size: 14px;
        }
        .signature {
            text-align: right;
            margin-top: 20px;
            font-weight: bold;
            padding-right: 30px;
        }
        .underline {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="receipt">
    <div class="header">
        <img src="{{ asset('images/bcm-logo.svg') }}" alt="Logo">
        <div class="header-text">
            <p>BAPTIST LITERATURE SERVICE : {{ auth()->user()->branch->branch_name }}</p>
            <p>{{ auth()->user()->branch->branch_address }}. Ph. {{ auth()->user()->branch->branch_phone }}</p>
            <p><span class="underline">LEHKHABU LAKNA / DAWNNA</span></p>
        </div>
    </div>
    <table class="details">
        <tr>
            <td>No. <strong class="underline">{{ $privateBook->file_no }}</strong></td>
            <td style="text-align: right;">Date: <strong class="underline">{{date('d-m-Y')}}</strong></td>
        </tr>
        <tr>
            <td>Title:</td>
            <td><strong class="underline">{{$privateBook->item->item_name}}</strong></td>
        </tr>
        <tr>
            <td>Author:</td>
            <td><strong class="underline">{{ $privateBook->author }}</strong></td>
        </tr>
        <tr>
            <td>Name of Supplier:</td>
            <td><strong class="underline">{{ $privateBook->receive_from }}</strong></td>
        </tr>
        <tr>
            <td>Address:</td>
            <td><strong class="underline">{{ $privateBook->address }}</strong></td>
        </tr>
        <tr>
            <td>Phone:</td>
            <td><strong class="underline">{{ $privateBook->phone_number }}</strong></td>
        </tr>
        <tr>
            <td>C.P.:</td>
            <td><strong class="underline">{{ $privateBook->mainStock->cost_price }}/-</strong></td>
        </tr>
        <tr>
            <td>S.P.:</td>
            <td><strong class="underline">{{ $privateBook->mainStock->mrp }}/-</strong></td>
        </tr>
        <tr>
            <td>Qnty.:</td>
            <td><strong class="underline">{{ $initialQuantity }} copies</strong></td>
        </tr>
    </table>
    <div class="signature">DAWNGTU</div>
    <div class="rules">
        <p><strong>HRIAT TUR PAWIMAWHTE:</strong></p>
        <p>1. Lehkhabu dah atanga thla 3 ral hnuah he lehkha keng hian a man ngaihven theih a ni ang.</p>
        <p>2. Lehkhabu dah atanga kum 2 hnuah hralh bangte chu a dahtu hnenah return theih a ni ang.</p>
        <p>3. Rukruk, Kangmei leh Khuarel chhiatna dangte avanga chhiatna/chhanna a awm a nih chuan Baptist Bookroom-in mawh a phur lovang.</p>
        <p>4. Lehkhabu dahtuin a duh thua lehkhabu a chah lêt (return) duh a nih chuan, a chah lêt man a chawi ang.</p>
    </div>
</div>
</body>
</html>
