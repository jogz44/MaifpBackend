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
            FORMAT(SUM(i.price_per_pcs * dt.quantity),2) AS total,
            dt.customer_id
        FROM
            tbl_daily_transactions dt
        INNER JOIN
            tbl_items i ON i.id = dt.item_id
        GROUP BY
            dt.transaction_id, dt.transaction_date, dt.customer_id ;
        ");

        DB::statement("
        CREATE OR REPLACE VIEW vw_patient_transactions_list as
        SELECT
            dt.transaction_id,
            dt.transaction_date,
            i.brand_name,
             i.generic_name,
            dt.quantity,
            i.price_per_pcs,
            format((i.price_per_pcs * dt.quantity),2) AS amount,
            dt.customer_id
        FROM
            tbl_daily_transactions dt
        INNER JOIN
            tbl_items i ON i.id = dt.item_id
        ORDER BY
            dt.transaction_id,  i.brand_name,  i.generic_name, dt.customer_id ;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_patients_overall_transactions;");
        DB::statement("DROP VIEW IF EXISTS vw_patient_transactions_list;");
    }
};
