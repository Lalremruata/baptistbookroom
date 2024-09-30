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
            $table->string('receiver_name')->nullable();
            $table->string('address')->nullable();
            $table->string('phone_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('private_book_accounts', function (Blueprint $table) {
            $table->dropColumn(['receiver_name', 'address', 'phone_number']);
        });
    }
};
