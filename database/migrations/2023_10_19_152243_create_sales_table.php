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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_stock_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('branch_id')
                ->constrained()
                ->onDelete('no action');
            $table->foreignId('user_id')->constrained();
            $table->foreignId('customer_id')
                ->constrained()
                ->onDelete('cascade')
                ->nullable();
            $table->integer('quantity');
            $table->decimal('discount', 8, 2)->nullable()->default(0);
            $table->decimal('total_amount', 8, 2)->nullable()->default(0);
            $table->string('payment_mode'); // Cash, Online, etc.
            $table->string('transaction_number')->nullable();
            $table->integer('memo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
