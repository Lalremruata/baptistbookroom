<?php

namespace App\Filament\Concerns;

trait CalculatesCartGst
{
    /**
     * Compute the GST-inclusive cart line figures.
     *
     * MRP is treated as GST-inclusive: selling price = (mrp * qty) - discount,
     * taxable amount ("rate") = sellingPrice / (1 + gstRate/100), and the GST
     * amount is the remainder. Shared by SalesCart and EstimateCart.
     */
    private function calculateGst(float $mrp, int $quantity, float $discount, float $gstRate): array
    {
        $totalMrp = $mrp * $quantity;
        $discountAmount = min($discount, $totalMrp);
        $sellingPrice = max(0, $totalMrp - $discountAmount);
        $taxableAmount = $gstRate > 0 ? $sellingPrice / (1 + ($gstRate / 100)) : $sellingPrice;

        return [
            'discount'               => $discountAmount,
            'selling_price'          => $sellingPrice,
            'gst_amount'             => $sellingPrice - $taxableAmount,
            'rate'                   => $taxableAmount,
            'total_amount_with_gst'  => $sellingPrice,
        ];
    }
}
