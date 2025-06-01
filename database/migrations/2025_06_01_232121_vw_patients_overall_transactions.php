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
        //
        DB::statement("
        CREATE OR REPLACE VIEW vw_patients_overall_transactions AS
        SELECT
            dt.transaction_id,
            dt.transaction_date,
            COUNT(*) AS released_items,
            SUM(i.price_per_pcs * dt.quantity) AS total
        FROM
            tbl_daily_transactions dt
        INNER JOIN
            tbl_items i ON i.id = dt.item_id
        GROUP BY
            dt.transaction_id, dt.transaction_date;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        DB::statement("DROP VIEW IF EXISTS vw_patients_overall_transactions;");
    }
};
