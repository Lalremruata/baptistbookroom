<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Estimate sales are now tied to an Item (from the catalog) plus a chosen
     * branch, not to a branch stock row. Add `item_id` and relax the required
     * `branch_stock_id` foreign key. `mrp` already exists on this table.
     */
    public function up(): void
    {
        Schema::table('estimate_sales', function (Blueprint $table) {
            $table->dropForeign(['branch_stock_id']);
        });

        Schema::table('estimate_sales', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_stock_id')->nullable()->change();
            $table->foreignId('item_id')->nullable()->after('branch_stock_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estimate_sales', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropColumn('item_id');
        });
    }
};
