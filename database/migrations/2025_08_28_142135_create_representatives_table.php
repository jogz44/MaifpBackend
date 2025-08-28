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
        Schema::create('representatives', function (Blueprint $table) {
            $table->id();
            $table->string('rep_name')->nullable();
            $table->string('rep_relationship')->nullable();
            $table->string('rep_contact')->nullable();
            $table->string('rep_barangay')->nullable();
            $table->string('rep_address')->nullable();
            $table->string('rep_purok')->nullable();
            $table->string('rep_street')->nullable();
            $table->string('rep_city')->nullable();
            $table->string('rep_province')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('representatives');
    }
};
