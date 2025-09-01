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
        Schema::create('lib_doctor', function (Blueprint $table) {
            //
            $table->id();
            $table->decimal('doctor_amount',15 ,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

            //
            Schema::dropIfExists('lib_doctor');

    }
};
