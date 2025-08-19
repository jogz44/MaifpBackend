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
        Schema::table('laboratory', function (Blueprint $table) {
            //
            $table->string('laboratory_type')->nullable()->after('new_consultation_id');
            $table->string('amount', 15, 2)->nullable()->after('laboratory_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratory', function (Blueprint $table) {
            //
            $table->dropColumn('laboratory_type')->nullable();
            $table->dropColumn('amount')->nullable();
        });
    }
};
