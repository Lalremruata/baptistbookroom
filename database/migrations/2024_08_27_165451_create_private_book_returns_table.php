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
        if ( ! Schema::hasTable('private_book_returns'))
    {
        Schema::create('private_book_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('private_book_id')->references('id')
            ->on('private_books')
            ->onDelete('cascade');
            $table->unsignedInteger('return_amount');
            $table->date('return_date');
            $table->timestamps();
        });
    }
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_book_returns');
    }
};
