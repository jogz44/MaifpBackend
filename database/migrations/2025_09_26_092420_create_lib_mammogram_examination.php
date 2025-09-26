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
        Schema::create('lib_mammogram_examination', function (Blueprint $table) {
            $table->id();
            $table->string('procedure')->nullable();
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
        Schema::dropIfExists('lib_mammogram_examination');
    }
};
