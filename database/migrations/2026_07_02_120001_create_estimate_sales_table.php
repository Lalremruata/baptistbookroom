<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Mirrors the final schema of `sales` (GST/rate/mrp columns included).
     * Estimate checkout writes here instead of `sales`. `customer_id` is kept
     * for structural parity but stays null (the estimate flow is fully isolated
     * and never creates Customer/CreditTransaction rows).
     */
    public function up(): void
    {
        Schema::create('estimate_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_stock_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('branch_id')
                ->constrained()
                ->onDelete('no action');
            $table->foreignId('user_id')->constrained();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->integer('quantity');
            $table->decimal('mrp', 10, 2)->nullable();
            $table->decimal('discount', 8, 2)->nullable()->default(0);
            $table->decimal('total_amount', 8, 2)->nullable()->default(0);
            $table->decimal('gst_rate', 5, 2)->nullable();
            $table->decimal('gst_amount', 10, 2)->nullable();
            $table->decimal('rate', 10, 2)->nullable();
            $table->decimal('total_amount_with_gst', 10, 2)->nullable();
            $table->string('payment_mode');
            $table->string('transaction_number')->nullable();
            $table->integer('memo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimate_sales');
    }
};
