<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            //
            DB::statement("ALTER TABLE transaction MODIFY COLUMN status ENUM('qualified', 'unqualified', 'assessment','Complete') NOT NULL DEFAULT 'assessment'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            //
            DB::statement("ALTER TABLE transaction MODIFY COLUMN status ENUM('Pending','Processing','Returned','Done','Medication','Complete') NOT NULL DEFAULT 'assessment'");
        });
    }
};
