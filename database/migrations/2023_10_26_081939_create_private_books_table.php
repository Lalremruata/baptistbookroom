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
        Schema::create('private_books', function (Blueprint $table) {
            $table->id();
            $table->string('receive_from');
            $table->string('author');
            $table->string('file_no');
            $table->unsignedInteger('quantity');
            $table->foreignId('item_id');
            $table->foreignId('main_stock_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_books');
    }
};
