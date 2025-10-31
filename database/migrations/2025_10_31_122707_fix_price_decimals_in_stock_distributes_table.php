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
        Schema::table('stock_distributes', function (Blueprint $table) {
            $table->decimal('cost_price', 10, 2)->change(); // 10 digits total, 2 decimal places
            $table->decimal('mrp', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_distributes', function (Blueprint $table) {
            //
        });
    }
};
