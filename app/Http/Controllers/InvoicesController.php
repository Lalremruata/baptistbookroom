<?php

namespace App\Http\Controllers;

use App\Models\StockDistributeCart;
use Carbon\Carbon;
use Illuminate\Http\Request;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
class InvoicesController extends Controller
{

    public function getInvoiceNumber()
   {
       $configPath = config_path('invoicenumber.php');
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
        $cartItems = StockDistributeCart::with('mainStock')->get();
        $client = new Party([
            'name'          => 'Baptist Literature Service : Bookroom : Aizawl',
            'phone'         => '0389-2345676',
            'custom_fields' => [
                'Address'        => 'Baptist Centre, MG Road, Khatla, Aizawl, Mizoram',
            ],
        ]);
        $customer = new Party([
            'name'          => $request['customer_name'],
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
            return Invoice::makeItem($cartItem->mainStock->item->item_name)
                ->title($cartItem->mainStock->item->item_name)
                ->pricePerUnit($cartItem->mrp)
                ->quantity($cartItem->quantity);
        })->toArray();

        $invoice = Invoice::make('receipt')
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
