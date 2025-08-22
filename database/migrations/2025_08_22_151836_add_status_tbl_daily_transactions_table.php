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
         Schema::table('tbl_daily_transactions', function (Blueprint $table) {

            $table->enum('status', ['Pending'])->default('Pending');

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('tbl_daily_transactions', function (Blueprint $table) {
            // Rollback: remove added columns
            $table->dropColumn('status');
        });
    }

};
