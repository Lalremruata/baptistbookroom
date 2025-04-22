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
        Schema::table('private_book_accounts', function (Blueprint $table) {
            $table->string('payment_mode')->nullable();
            $table->string('transaction_number')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('private_book_accounts', function (Blueprint $table) {
            $table->dropColumn(['payment_mode', 'transaction_number', 'account_number', 'ifsc_code']);
        });
    }
};
