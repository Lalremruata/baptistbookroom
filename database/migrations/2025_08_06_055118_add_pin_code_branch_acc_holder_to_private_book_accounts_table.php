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
            $table->integer('quantity')->default(0); // Add quantity column
            $table->char('pin_code', 6)->nullable();
            $table->string('branch_name')->nullable(); //Bank branch name
            $table->string('account_holder')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('private_book_accounts', function (Blueprint $table) {
            //
        });
    }
};
