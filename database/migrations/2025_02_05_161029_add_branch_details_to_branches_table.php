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
        Schema::table('branches', function (Blueprint $table) {
            // Add branch details to branches table
            $table->string('branch_address')->nullable()->after('branch_name');
            $table->string('branch_phone')->nullable()->after('branch_address');
            $table->string('branch_email')->nullable()->after('branch_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['branch_address', 'branch_phone', 'branch_email']);
        });
    }
};
