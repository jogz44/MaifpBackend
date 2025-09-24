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
        Schema::table('laboratory_details', function (Blueprint $table) {
            $table->decimal('service_fee', 15,2)->nullable()->after('amount');
            $table->decimal('total_amount' , 15,2)->nullable()->after('service_fee');
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratory_details', function (Blueprint $table) {
            $table->dropColumn(['service_fee', 'total_amount']);
        });
    }
};
