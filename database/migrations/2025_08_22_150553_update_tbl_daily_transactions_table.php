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
        Schema::table('tbl_daily_transactions', function (Blueprint $table) {

            // $table->string('item_description')->nullable()->after('transaction_id');
            $table->decimal('total',15,2)->nullable()->after('transaction_date');

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
            $table->dropColumn([ 'status', 'total']);


        });
    }
};
