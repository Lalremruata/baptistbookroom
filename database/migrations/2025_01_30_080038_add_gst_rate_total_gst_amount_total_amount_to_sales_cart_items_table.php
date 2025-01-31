<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales_cart_items', function (Blueprint $table) {
            $table->decimal('gst_rate', 8, 2)->default(0)->after('discount');
            $table->decimal('gst_amount', 8, 2)->default(0)->after('gst_rate');
            $table->decimal('total_amount_with_gst', 8, 2)->default(0)->after('gst_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_cart_items', function (Blueprint $table) {
            //
        });
    }
};
