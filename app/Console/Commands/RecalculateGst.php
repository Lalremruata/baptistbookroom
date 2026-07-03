<?php

namespace App\Console\Commands;

use App\Models\Sale;
use App\Models\SalesCartItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecalculateGst extends Command
{
    protected $signature = 'gst:recalculate {--execute : Apply the changes (default is dry run)}';

    protected $description = 'Recalculate GST amounts for discounted sales where GST was incorrectly computed on full MRP instead of discounted price';

    public function handle(): int
    {
        $execute = $this->option('execute');

        if (!$execute) {
            $this->info('DRY RUN — no changes will be made. Use --execute to apply.');
            $this->newLine();
        }

        $this->backfillMrp($execute);
        $this->newLine();
        $this->fixSales($execute);
        $this->newLine();
        $this->fixSalesCartItems($execute);

        return Command::SUCCESS;
    }

    private function backfillMrp(bool $execute): void
    {
        $this->info('=== BACKFILL MRP ON SALES TABLE ===');

        $count = Sale::whereNull('mrp')->count();

        if ($count === 0) {
            $this->info('All sales records already have MRP set. No backfill needed.');
            return;
        }

        $this->info("Found {$count} sales record(s) with NULL mrp.");

        if ($execute) {
            // First try: backfill from branch_stocks where the record still exists
            $fromBranchStock = DB::table('sales')
                ->join('branch_stocks', 'sales.branch_stock_id', '=', 'branch_stocks.id')
                ->whereNull('sales.mrp')
                ->update(['sales.mrp' => DB::raw('branch_stocks.mrp')]);
            $this->info("Backfilled {$fromBranchStock} record(s) from branch_stocks.");

            // Fallback: derive MRP from total_amount for remaining NULL records
            // Since total_amount = mrp * quantity - discount, then mrp = (total_amount + discount) / quantity
            $fromCalculation = DB::table('sales')
                ->whereNull('mrp')
                ->where('quantity', '>', 0)
                ->update(['mrp' => DB::raw('ROUND((total_amount + COALESCE(discount, 0)) / quantity, 2)')]);
            $this->info("Backfilled {$fromCalculation} record(s) by calculating from total_amount.");
        } else {
            $this->warn("{$count} sales record(s) would have MRP backfilled.");
        }
    }

    private function fixSales(bool $execute): void
    {
        $this->info('=== SALES TABLE ===');

        $sales = Sale::where('discount', '>', 0)
            ->where('gst_rate', '>', 0)
            ->get();

        if ($sales->isEmpty()) {
            $this->info('No affected sales records found.');
            return;
        }

        $this->info("Found {$sales->count()} sales record(s) with discount > 0 and gst_rate > 0.");
        $this->newLine();

        $rows = [];
        $updatedCount = 0;

        foreach ($sales as $sale) {
            // total_amount is the GST-inclusive price after discount (MRP*qty - discount)
            $gstInclusivePrice = (float) $sale->total_amount;
            $gstRate = (float) $sale->gst_rate;

            // Correct calculation: extract GST from the inclusive price
            $taxableAmount = $gstInclusivePrice / (1 + ($gstRate / 100));
            $correctGstAmount = round($gstInclusivePrice - $taxableAmount, 2);
            $correctRate = round($taxableAmount, 2);

            $oldGstAmount = round((float) $sale->gst_amount, 2);
            $oldRate = round((float) $sale->rate, 2);

            $gstChanged = abs($oldGstAmount - $correctGstAmount) > 0.01;
            $rateChanged = abs($oldRate - $correctRate) > 0.01;

            if ($gstChanged || $rateChanged) {
                $rows[] = [
                    $sale->id,
                    $sale->memo,
                    number_format($gstInclusivePrice, 2),
                    $sale->discount,
                    $gstRate . '%',
                    number_format($oldGstAmount, 2) . ' → ' . number_format($correctGstAmount, 2),
                    number_format($oldRate, 2) . ' → ' . number_format($correctRate, 2),
                ];

                if ($execute) {
                    $sale->gst_amount = $correctGstAmount;
                    $sale->rate = $correctRate;
                    $sale->total_amount_with_gst = $gstInclusivePrice;
                    $sale->save();
                    $updatedCount++;
                }
            }
        }

        if (empty($rows)) {
            $this->info('All sales records already have correct GST values. No changes needed.');
            return;
        }

        $this->table(
            ['ID', 'Memo', 'Total Amount', 'Discount', 'GST Rate', 'GST Amount (old → new)', 'Rate (old → new)'],
            $rows
        );

        $this->newLine();
        if ($execute) {
            $this->info("Updated {$updatedCount} sales record(s).");
        } else {
            $this->warn(count($rows) . " sales record(s) would be updated.");
        }
    }

    private function fixSalesCartItems(bool $execute): void
    {
        $this->info('=== SALES CART ITEMS TABLE ===');

        $cartItems = SalesCartItem::where('discount', '>', 0)
            ->where('gst_rate', '>', 0)
            ->get();

        if ($cartItems->isEmpty()) {
            $this->info('No affected cart items found.');
            return;
        }

        $this->info("Found {$cartItems->count()} cart item(s) with discount > 0 and gst_rate > 0.");
        $this->newLine();

        $rows = [];
        $updatedCount = 0;

        foreach ($cartItems as $item) {
            // selling_price is the GST-inclusive price after discount
            $gstInclusivePrice = (float) $item->selling_price;
            $gstRate = (float) $item->gst_rate;

            $taxableAmount = $gstInclusivePrice / (1 + ($gstRate / 100));
            $correctGstAmount = round($gstInclusivePrice - $taxableAmount, 2);
            $correctRate = round($taxableAmount, 2);

            $oldGstAmount = round((float) $item->gst_amount, 2);
            $oldRate = round((float) $item->rate, 2);

            $gstChanged = abs($oldGstAmount - $correctGstAmount) > 0.01;
            $rateChanged = abs($oldRate - $correctRate) > 0.01;

            if ($gstChanged || $rateChanged) {
                $rows[] = [
                    $item->id,
                    $item->user_id,
                    number_format($gstInclusivePrice, 2),
                    $item->discount,
                    $gstRate . '%',
                    number_format($oldGstAmount, 2) . ' → ' . number_format($correctGstAmount, 2),
                    number_format($oldRate, 2) . ' → ' . number_format($correctRate, 2),
                ];

                if ($execute) {
                    $item->gst_amount = $correctGstAmount;
                    $item->rate = $correctRate;
                    $item->total_amount_with_gst = $gstInclusivePrice;
                    $item->save();
                    $updatedCount++;
                }
            }
        }

        if (empty($rows)) {
            $this->info('All cart items already have correct GST values. No changes needed.');
            return;
        }

        $this->table(
            ['ID', 'User ID', 'Selling Price', 'Discount', 'GST Rate', 'GST Amount (old → new)', 'Rate (old → new)'],
            $rows
        );

        $this->newLine();
        if ($execute) {
            $this->info("Updated {$updatedCount} cart item(s).");
        } else {
            $this->warn(count($rows) . " cart item(s) would be updated.");
        }
    }
}
