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
        Schema::create('deleted_activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->longText('subject')->nullable(); // full subject JSON
            $table->longText('causer')->nullable();  // full causer JSON
            $table->json('properties')->nullable();
            $table->index('log_name');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('deleted_at')->nullable(); // when we archived it
            // $table->timestamps('updated_at'); // 👈 this will add created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deleted_activity_log');
    }
};
