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
        Schema::create('medication_details', function (Blueprint $table) {
            $table->id();
            $table->string('item_description')->nullable();
            $table->foreignId('patient_id')->nullable()->constrained('patient')->onDelete('cascade');
            $table->integer('quantity')->nullable();
            $table->string('unit')->nullable();
            $table->date('transaction_date')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('cascade');
            // $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_details');
    }
};
