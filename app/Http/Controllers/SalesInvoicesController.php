<?php

namespace App\Http\Controllers;

use App\Models\SalesCartItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;

class SalesInvoicesController extends Controller
{
    public function getInvoiceNumber()
   {
       $configPath = config_path('saleinvoicenumbergenerator.php');
       $config = include($configPath);

       $invoiceNumber = $config['number'];
       $newInvoiceNumber = $invoiceNumber + 1;

       $newConfig = "<?php\n\nreturn [\n    'number' => $newInvoiceNumber,\n];\n";
       file_put_contents($configPath, $newConfig);

       return $invoiceNumber;
   }
    public function downloadInvoice(Request $request)
    {
        $invoiceNumber = $this->getInvoiceNumber();
        $date = Carbon::now();
        $formattedYear = $date->format('y');
        $cartItems = SalesCartItem::with('branchStock')->get();
        $client = new Party([
            'name'          => Auth()->user()->branch->branch_name,
            // 'phone'         => '0389-2345676',
            // 'custom_fields' => [
            //     'Address'        => 'Baptist Centre, MG Road, Khatla, Aizawl, Mizoram',
            // ],
        ]);
        $customer = new Party([
            'name'          => $request['name'],
            'address'       => $request['address'],
            'custom_fields' => [
                'Bill number' =>  $invoiceNumber.'/'.$formattedYear,
            ],
        ]);
        $notes = [
            'your multiline',
            'additional notes',
            'in regards of delivery or something else',
        ];
        $notes = implode("<br>", $notes);

        $items = $cartItems->map(function ($cartItem) {
            return Invoice::makeItem($cartItem->branchStock->mainStock->item->item_name)
                ->title($cartItem->branchStock->mainStock->item->item_name)
                ->pricePerUnit($cartItem->selling_price)
                ->quantity($cartItem->quantity);
        })->toArray();

        $invoice = Invoice::make('receipt')
        ->template('salesinvoice')
            ->seller($client)
            ->buyer($customer)
            ->serialNumberFormat('{SEQUENCE}/{SERIES}')
            ->dateFormat('d/m/Y')
            ->currencySymbol('₹')
            ->currencyCode('Rupees')
            ->currencyFraction('paise')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator(',')
            ->addItems($items)
            ->series($formattedYear)
            ->sequence($invoiceNumber)
            ->delimiter('/')
            ->logo(public_path('/images/bcm-logo.svg'));

        return $invoice->download();
    }
}
