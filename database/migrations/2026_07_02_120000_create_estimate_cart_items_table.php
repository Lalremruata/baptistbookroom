<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Mirrors the final schema of `sales_cart_items` (prices already decimal,
     * GST/rate/total columns included). Used by the Estimate/Quotation cart,
     * which never touches real stock or real sales.
     */
    public function up(): void
    {
        Schema::create('estimate_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_stock_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->integer('quantity')->unsigned();
            $table->decimal('cost_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('gst_rate', 8, 2)->default(0);
            $table->decimal('gst_amount', 8, 2)->default(0);
            $table->decimal('rate', 8, 2)->default(0);
            $table->decimal('total_amount_with_gst', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimate_cart_items');
    }
};
