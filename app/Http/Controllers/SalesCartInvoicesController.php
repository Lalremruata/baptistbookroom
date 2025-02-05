<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Memo;
use App\Models\SalesCartItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use Filament\Notifications\Notification;

class SalesCartInvoicesController extends Controller
{
    public function generatePdf(Request $request)
    {
        $records = SalesCartItem::with('branchStock')->where('branch_id',auth()->user()->branch_id)->get();
        // Memo number
        $lastMemo = Memo::where('branch_id', auth()->user()->branch_id)
        ->lockForUpdate() // Lock the row to prevent concurrent updates
        ->orderBy('memo', 'desc')
        ->first();

        $branch = Branch::find(auth()->user()->branch_id);

        // Generate the next memo number for the current branch
        $memoNumber = $lastMemo ? $lastMemo->memo + 1 : 1000;
        $pdf = Pdf::loadView('vendor.invoices.templates.salesCartInvoice', ['request' => $request, 'records' => $records, 'memoNumber' => $memoNumber, 'branch' => $branch]);

        // Return PDF as a download response
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'sale_receipts.pdf');
    }
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
        $cartItems = SalesCartItem::with('branchStock')->where('branch_id',auth()->user()->branch_id)->get();
        if ($cartItems->isEmpty()) {
            Notification::make()
            ->danger()
            ->title('Error')
            ->body('No items found in the cart to generate an invoice.')
            ->send();
            return redirect()->back();
        }
        $invoiceNumber = $this->getInvoiceNumber();
        $date = Carbon::now();
        $formattedYear = $date->format('y');
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
                'GST' =>  $request['gst_number'],
            ],
        ]);
        $notes = [
            'your multiline',
            'additional notes',
            'in regards of delivery or something else',
        ];
        $notes = implode("<br>", $notes);

        $items = $cartItems->map(function ($cartItem) {
    
            // Add GST details to the item description
            $description = "GST Rate: {$cartItem->gst_rate}% | GST Amount: ₹{$cartItem->gst_amount}";
            // $gstRate = $cartItem->gst_rate;
            return Invoice::makeItem($cartItem->branchStock->mainStock->item->item_name)
                ->title($cartItem->branchStock->mainStock->item->item_name)
                ->description($description)
                ->pricePerUnit($cartItem->selling_price)
                ->quantity($cartItem->quantity)
                ->tax($cartItem->gst_amount)
                ->subTotalPrice($cartItem->total_amount_with_gst);
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
            // ->taxRate($cartItems->sum('gst_rate'))
            // ->totalTaxes($cartItems->sum('gst_amount'))
            // ->taxableAmount($cartItems->sum('total_amount_with_gst'))
            ->logo(public_path('/images/bcm-logo.svg'));

        return $invoice->download();
    }
}
