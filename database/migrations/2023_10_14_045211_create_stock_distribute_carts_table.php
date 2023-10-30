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
        Schema::create('stock_distribute_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('main_stock_id')->constrained;
            $table->foreignId('user_id')->constrained;
            $table->integer('quantity');
            $table->unsignedInteger('cost_price');
            $table->unsignedInteger('mrp');
            $table->string('batch');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_distribute_carts');
    }
};
