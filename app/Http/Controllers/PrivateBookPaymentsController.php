<?php

namespace App\Http\Controllers;

use App\Models\PrivateBookAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PrivateBookPaymentsController extends Controller
{
    public function downloadInvoice(Request $request, PrivateBookAccount $privateBookAccount)
    {
        $data = [
            'invoiceNumber'  => str_pad($privateBookAccount->id, 4, '0', STR_PAD_LEFT) . '/' . Carbon::now()->format('y'),
            'itemDescription' => $privateBookAccount->privateBook->item->item_name,
            'itemNo'         => $privateBookAccount->privateBook->file_no,
            'date'           => Carbon::now()->format('d/m/Y'),
            'receiverName'   => $privateBookAccount->receiver_name,
            'receiverAddress'=> $privateBookAccount->address,
            'receiverPinCode'=> $privateBookAccount->pin_code,
            'receiverPhone'  => $privateBookAccount->phone_number,
            'returnAmount'   => $privateBookAccount->return_amount,
            'payment_mode'   => $privateBookAccount->payment_mode,
            'transaction_number' => $privateBookAccount->transaction_number,
            'account_number' => $privateBookAccount->account_number,
            'account_holder' => $privateBookAccount->account_holder,
            'branch_name'    => $privateBookAccount->branch_name,
            'ifsc_code'      => $privateBookAccount->ifsc_code,
            'returnDate'     => Carbon::parse($privateBookAccount->return_date)->format('d/m/Y'),
        ];

        // Generate PDF from the Blade view
        $pdf = PDF::loadView('vendor.invoices.templates.privateBookPaymentInvoice', $data)
                 ->setPaper('a5', 'landscape');

        $fileName = 'receipt_' . $privateBookAccount->id . '.pdf';

        // Download the PDF
        return $pdf->download($fileName);
    }
}