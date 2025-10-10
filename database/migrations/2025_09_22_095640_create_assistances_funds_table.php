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
        Schema::create('assistances_funds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->nullable()->constrained('transaction')->onDelete('cascade');
            $table->string('fund_source');
            $table->decimal('fund_amount', 10, 2)->nullable();
            $table->timestamps();
            // $table->foreign('assistance_id')->references('id')->on('assistances')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assistances_funds');
    }
};
