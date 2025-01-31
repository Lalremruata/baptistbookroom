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
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('gst_rate', 5, 2)->nullable()->after('total_amount');
            $table->decimal('gst_amount', 10, 2)->nullable()->after('gst_rate');
            $table->decimal('total_amount_with_gst', 10, 2)->nullable()->after('gst_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['gst_rate', 'gst_amount', 'total_amount_with_gst']);
        });
    }
};
