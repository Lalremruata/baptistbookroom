<?php

namespace App\Http\Controllers;

use App\Models\PrivateBookReturn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PrivateBookReturnsController extends Controller
{
    public function downloadInvoice(Request $request, PrivateBookReturn $privateBookReturn)
    {
        $data = [
            'invoiceNumber'  => $privateBookReturn->id . '/' . Carbon::now()->format('y'),
            'date'           => Carbon::now()->format('d/m/Y'),
            'receiverName'   => $privateBookReturn->receiver_name,
            'receiverAddress'=> $privateBookReturn->address,
            'receiverPhone'  => $privateBookReturn->phone_number,
            'returnAmount'   => $privateBookReturn->return_amount,
            'returnDate'     => Carbon::parse($privateBookReturn->return_date)->format('d/m/Y'),
        ];

        // Generate PDF from the Blade view
        $pdf = PDF::loadView('vendor.invoices.templates.privateBookReturnInvoice', $data);

        // Download the generated PDF
        return $pdf->download('invoice.pdf');
    }
}
