<?php

namespace App\Http\Controllers;

use App\Models\BranchStock;
use App\Models\MainStock;
use App\Models\PrivateBook;
use App\Models\PrivateBookReturn;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class PrivateBookAccountReceiptController extends Controller
{
    public function downloadReceipt(PrivateBook $privateBook)
    {
        $itemId = $privateBook->id;

        // Fetch main stock, branch stock, and total sales in a single query
        $mainStock = MainStock::where('id', $privateBook->main_stock_id)->first(['quantity','cost_price', 'mrp']);

        $branchStockQuantity = BranchStock::where('main_stock_id', $privateBook->main_stock_id)->sum('quantity');

        // Total sale quantity based on item_id
        $totalSale = Sale::whereHas('branchStock.mainStock.item', function ($query) use ($itemId) {
            $query->where('items.id', $itemId);
        })->sum('quantity');

        // Total returns for the book
        $totalReturns = PrivateBookReturn::where('private_book_id', $privateBook->id)->sum('return_amount');

        // Calculate total quantity using null coalescing to handle potential null values

        $initialQuantity = ($mainStock->quantity ?? 0)
            + $branchStockQuantity
            + $totalSale
            + $totalReturns;
        // Pass the data to the view
        $pdf = Pdf::loadView('vendor.invoices.templates.bookAccountReceipt', compact('privateBook','initialQuantity'));

        // Download the PDF
        return $pdf->download("receipt-{$privateBook->id}.pdf");
    }
}
