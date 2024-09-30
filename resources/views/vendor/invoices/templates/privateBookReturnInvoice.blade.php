<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        /* Add your invoice styling here */
        body {
            font-family: 'Arial', sans-serif;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            color: #555;
        }
        table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }
        table td {
            padding: 5px;
            vertical-align: top;
        }
        .logo {
            width: 100px;
            height: auto;
        }
        .total {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table>
            <tr>
                <td>
                    <!-- Adding the logo at the top left corner -->
                    <img src="{{ asset('images/bcm-logo.svg') }}" alt="Logo" class="logo">
                </td>
            </tr>
            <tr>
                <td>
                    <h2>Baptist Literature Service : Bookroom</h2>
                    <p>
                        Address: Baptist House, MG Road, Khatla, Aizawl, Mizoram<br>
                        Phone: 0389-2345676
                    </p>
                </td>
                <td>
                    <h3>Invoice #{{ $invoiceNumber }}</h3>
                    <p>Date: {{ $date }}</p>
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td>
                    <h3>Bill To:</h3>
                    <p>
                        {{ $receiverName }}<br>
                        Address: {{ $receiverAddress }}<br>
                        Phone: {{ $receiverPhone }}
                    </p>
                </td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Return Amount</td>
                    <td>{{ $returnAmount }}</td>
                </tr>
                <tr>
                    <td>Return Date</td>
                    <td>{{ $returnDate }}</td>
                </tr>
            </tbody>
        </table>

        <hr>
    </div>
</body>
</html>
