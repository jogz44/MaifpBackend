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
        Schema::create('budget_additional_funds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->nullable()->constrained('budget')->onDelete('cascade');
            $table->decimal('release', 15, 2)->nullable();
            // $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_additional_funds');
    }
};
