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
        Schema::create('medication', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transaction')->nullable()->onDelete('cascade');
            $table->foreignId('new_consultation_id')->constrained('new_consultation')->nullable()->onDelete('cascade');
            $table->bigInteger('item_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('capsule');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication');
    }
};
