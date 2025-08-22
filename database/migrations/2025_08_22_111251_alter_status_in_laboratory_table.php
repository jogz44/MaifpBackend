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
        Schema::table('laboratory', function (Blueprint $table) {
            //
            DB::statement("ALTER TABLE laboratory MODIFY COLUMN status ENUM('Pending','Processing','Returned','Done','Medication') NOT NULL DEFAULT 'Pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratory', function (Blueprint $table) {
            //
            DB::statement("ALTER TABLE laboratory MODIFY COLUMN status ENUM('Pending','Processing','Returned','Done') NOT NULL DEFAULT 'Pending'");
        });
    }
};
