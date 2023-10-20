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
        Schema::create('sales_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreign('branch_id')->references('branch_id')->on('branch_stocks')->onDelete('cascade');
            $table->foreign('item_id')->references('item_id')->on('branch_stocks')->onDelete('cascade');
            $table->integer('quantity')->unsigned();
            $table->integer('selling_price')->unsigned();
            $table->integer('discount')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_cart_items');
    }
};
