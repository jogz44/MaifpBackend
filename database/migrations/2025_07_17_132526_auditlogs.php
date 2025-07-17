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
        //
        Schema::create('tbl_auditlogs', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->string('table_name');
            $table->unsignedBigInteger('user_id');
            $table->string('changes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_auditlogs');
    }
};
