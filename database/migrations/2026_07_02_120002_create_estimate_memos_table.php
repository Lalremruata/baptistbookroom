<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Isolated invoice counter for the Estimate/Quotation flow, so estimate
     * memo numbering never advances the real `memos` counter.
     */
    public function up(): void
    {
        Schema::create('estimate_memos', function (Blueprint $table) {
            $table->id();
            $table->integer('memo');
            $table->foreignId('branch_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimate_memos');
    }
};
