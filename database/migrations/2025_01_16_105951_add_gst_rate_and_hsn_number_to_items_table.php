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
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('gst_rate', 5, 2)->default(0.00)->after('barcode'); // GST rate column
            $table->string('hsn_number', 15)->nullable()->after('gst_rate'); // HSN number column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['gst_rate', 'hsn_number']);
        });
    }
};
