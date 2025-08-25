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
        Schema::create('billing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->nullable()->constrained('patient')->onDelete('cascade');
            $table->foreignId('transaction_id')->nullable()->constrained('transaction')->onDelete('cascade');
            $table->decimal('consultation_amount', 15, 2)->nullable();
            $table->decimal('laboratory_amount', 15, 2)->nullable();
            $table->decimal('medication_amount', 15, 2)->nullable();
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->enum('status', ['unfunded','funded'])->default('unfunded');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing');
    }
};
