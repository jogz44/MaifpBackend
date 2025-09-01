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
        Schema::create('lib_laboratory', function (Blueprint $table) {
            //
            $table->id();
            $table->string('lab_name')->nullable();
            $table->decimal('lab_amount',15,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
            Schema::dropIfExists('lib_laboratory');
    }
};
