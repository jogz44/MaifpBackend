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
        Schema::create('laboratory_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transaction')->onDelete('cascade');
            $table->foreignId('new_consultation_id')->nullable()->constrained('new_consultation')->onDelete('cascade');
            $table->string('laboratory_type')->nullable();
            $table->string('amount', 15, 2)->nullable();
            // $table->enum('status', ['Pending', 'Processing', 'Returned', 'Done', 'Medication'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratory_details');
    }
};
