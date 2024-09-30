<?php

namespace App\Http\Controllers;

use App\Models\PrivateBookAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;

class PrivateBookPaymentsController extends Controller
{
    public function downloadInvoice(Request $request, PrivateBookAccount $privateBookAccount)
    {
        $date = Carbon::now();
        $formattedYear = $date->format('y');

        // Define the seller (client) information
        $client = new Party([
            'name'          => 'Baptist Literature Service : Bookroom : Aizawl',
            'phone'         => '0389-2345676',
            'custom_fields' => [
                'Address' => 'Baptist House, MG Road, Khatla, Aizawl, Mizoram',
            ],
        ]);

        // Define the buyer (customer) information
        $customer = new Party([
            'name'          => $privateBookAccount->receiver_name,
            'phone'         => $privateBookAccount->phone_number,
            'address'       => $privateBookAccount->address,
            'custom_fields' => [
                'Bill number' => $privateBookAccount->id.'/'.$formattedYear,
            ],
        ]);

        // Get the private book account and related private book details
        $privateBookItem = PrivateBookAccount::with('privateBook')->where('id', $privateBookAccount->id)->first();

        // Parse the return_date as Carbon instance
        $returnDate = Carbon::parse($privateBookItem->return_date)->format('d/m/Y');

        // Manually creating the details for return amount and date (minimal pricePerUnit set to 1)
        $item = InvoiceItem::make('Return')
            ->title('Amount: ₹' . $privateBookItem->return_amount)
            ->description('Return Date: ' . $returnDate)
            ->pricePerUnit(1)  // Set minimal price per unit to avoid errors (placeholder)
            ->quantity(1);      // Set quantity to 1 as it's not relevant in this case

        // Create the invoice
        $invoice = Invoice::make('receipt')
            ->template('privateBookPaymentInvoice') // Ensure this template fits your need
            ->seller($client)
            ->buyer($customer)
            ->serialNumberFormat('{SEQUENCE}/{SERIES}')
            ->dateFormat('d/m/Y')
            ->addItem($item) // Just adding the item with the amount and date
            ->series($formattedYear)
            ->delimiter('/')
            ->logo(public_path('/images/bcm-logo.svg'))
            ->notes('Thank you for returning the book.');

        // Return the generated invoice for download
        return $invoice->download();
    }
}


