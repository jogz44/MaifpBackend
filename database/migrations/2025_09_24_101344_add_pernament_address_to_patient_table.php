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
        Schema::table('patient', function (Blueprint $table) {
            //
            $table->string('permanent_street')->nullable()->after('province');
            $table->string('permanent_purok')->nullable()->after('permanent_street');
            $table->string('permanent_barangay')->nullable()->after('permanent_purok');
            $table->string('permanent_city')->nullable()->after('permanent_barangay');
            $table->string('permanent_province')->nullable()->after('permanent_city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient', function (Blueprint $table) {
            //
            $table->dropColumn(['permanent_street', 'permanent_purok', 'permanent_barangay', 'permanent_city', 'permanent_province']);
        });
    }
};
