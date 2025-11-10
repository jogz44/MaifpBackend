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
        Schema::create('lab_ultrasound_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('new_consultation_id')->nullable()->constrained('new_consultation')->onDelete('cascade');
            $table->string('body_parts')->nullable();
            // $table->string('item_description')->nullable();
            $table->decimal('rate', 15, 2)->nullable();
            $table->decimal('service_fee', 15, 2)->nullable();
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_ultrasound_details');
    }
};
