<?php

namespace App\Http\Controllers;

use App\Models\PrivateBookAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class PrivateBookPaymentsController extends Controller
{
    public function downloadInvoice(Request $request, PrivateBookAccount $privateBookAccount)
    {
        $data = [
            'invoiceNumber'  => $privateBookAccount->id . '/' . Carbon::now()->format('y'),
            'date'           => Carbon::now()->format('d/m/Y'),
            'receiverName'   => $privateBookAccount->receiver_name,
            'receiverAddress'=> $privateBookAccount->address,
            'receiverPhone'  => $privateBookAccount->phone_number,
            'returnAmount'   => $privateBookAccount->return_amount,
            'returnDate'     => Carbon::parse($privateBookAccount->return_date)->format('d/m/Y'),
        ];

        // Generate PDF from the Blade view
        $pdf = PDF::loadView('vendor.invoices.templates.privateBookPaymentInvoice', $data);

        // Download the generated PDF
        return $pdf->download('invoice.pdf');
    }
}


