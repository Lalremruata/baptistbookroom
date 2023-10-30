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
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_stock_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->integer('quantity_returned')->nullable();
            $table->date('return_date');
            $table->boolean('is_approved')->default(false);
            $table->string('return_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_items');
    }
};
