<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The Estimate cart now pulls from the Item catalog (with a branch chosen
     * from a dropdown) instead of branch stock. Add `item_id` + a suggested
     * `mrp`, and relax `branch_stock_id` so it is no longer required.
     */
    public function up(): void
    {
        Schema::table('estimate_cart_items', function (Blueprint $table) {
            $table->dropForeign(['branch_stock_id']);
        });

        Schema::table('estimate_cart_items', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_stock_id')->nullable()->change();
            $table->foreignId('item_id')->nullable()->after('branch_stock_id')->constrained();
            $table->decimal('mrp', 10, 2)->nullable()->after('discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estimate_cart_items', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropColumn(['item_id', 'mrp']);
        });
    }
};
