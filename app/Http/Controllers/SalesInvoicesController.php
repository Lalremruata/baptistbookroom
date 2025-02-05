<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\SalesCartItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesInvoicesController extends Controller
{
    public function generatePdf(Collection $records, array $request)
    {
        // dd($records[0]->customer->customer_name);
        // Ensure all text fields are properly encoded
        // $records->transform(function ($record) {
        //     $data = $record->toArray(); // Convert record to array
        //     foreach ($data as $key => $value) {
        //         if (is_string($value)) {
        //             $data[$key] = mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
        //         }
        //     }
        //     return (object) $data; // Return as object
        // });

        // Generate PDF from Blade template
        $branch = Branch::find($records[0]->branch_id);
        $pdf = Pdf::loadView('vendor.invoices.templates.sale_receipt', ['records' => $records, 'data' => $request, 'branch' => $branch]);

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
   public function downloadInvoice(Collection $records)
   {
       // Check if records are empty
       if ($records->isEmpty()) {
           return redirect()->back()->with('error', 'No records selected for invoice generation.');
       }
   
       // Ensure relationships are loaded
       $records->load([
           'branch',
           'branchStock.mainStock.item',
       ]);
   
       // Generate invoice number
       $invoiceNumber = $this->getInvoiceNumber();
       $date = Carbon::now();
       $formattedYear = $date->format('y');
       // Define seller (client) and buyer (customer)
       $client = new Party([
           'name' => $records[0]->branch->branch_name,
       ]);
   
       $customer = new Party([
           'name'    => $records[0]->customer_name,
           'address' => $records[0]->address,
       ]);
   
       // Define invoice notes
       $notes = [
           'Your multiline',
           'Additional notes',
           'In regards to delivery or something else.',
       ];
       $notes = implode("<br>", $notes);
   
       // Map records to invoice items
       $records->each(function ($record) {
        foreach ($record->getAttributes() as $key => $value) {
            if (is_string($value)) {
                $record->$key = mb_convert_encoding($value, 'UTF-8', 'auto');
            }
        }
    });
       $items = $records->map(function ($record) {
           $description = "GST Rate: {$record->gst_rate}% | GST Amount: ₹{$record->gst_amount}";
   
           return Invoice::makeItem($record->branchStock->mainStock->item->item_name)
               ->title($record->branchStock->mainStock->item->item_name)
               ->description($description)
               ->pricePerUnit($record->total_amount)
               ->quantity($record->quantity)
               ->tax($record->gst_amount)
               ->subTotalPrice($record->total_amount_with_gst);
       })->toArray();
   
       // Generate the invoice
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
   
       // Download the invoice
       return $invoice->download();
   }
}
