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
        Schema::create('branch_stocks', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->unsignedInteger('cost_price');
            $table->unsignedInteger('mrp');
            $table->unsignedInteger('batch');
            $table->string('barcode');
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('main_stock_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_stocks');
    }
};
