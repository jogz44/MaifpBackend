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
        Schema::table('new_consultation', function (Blueprint $table) {
            //
            DB::statement("ALTER TABLE new_consultation MODIFY COLUMN status ENUM('Done', 'Processing', 'Pending', 'Returned', 'Medication') DEFAULT 'Pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('new_consultation', function (Blueprint $table) {
            //
            DB::statement("ALTER TABLE new_consultation MODIFY COLUMN status ENUM('Done', 'Processing', 'Pending', 'Returned') DEFAULT 'Pending'");
        });
    }
};
