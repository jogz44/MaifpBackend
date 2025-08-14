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
        Schema::create('patient', function (Blueprint $table) {
            $table->id();
            $table->string('firstname')->required();
            $table->string('lastname')->required();
            $table->string('middlename')->nullable();
            $table->string('ext')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('contact_number')->nullable();
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->boolean('is_not_tagum')->default(false);
            $table->string('street')->nullable();
            $table->string('purok')->nullable();
            $table->string('barangay')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('category')->nullable();
            $table->boolean('is_pwd')->default(false);
            $table->boolean('is_solo')->default(false);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient');
    }
};
