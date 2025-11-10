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
        Schema::create('assistances', function (Blueprint $table) {
            $table->id();
            // $table->string('gl_number')->nullable();
            $table->string('gl_lgu')->nullable();
            $table->string('gl_cong')->nullable();
            $table->foreignId('patient_id')->nullable()->constrained('patient')->onDelete('cascade');
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('cascade');
            $table->json('medication')->nullable();
            $table->decimal('consultation_amount', 10, 2)->default(0);
            $table->decimal('medication_total', 10, 2)->default(0);
            $table->decimal('total_billing', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('final_billing', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assistances');
    }
};
