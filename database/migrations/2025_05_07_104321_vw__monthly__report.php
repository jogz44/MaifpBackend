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
        CREATE OR REPLACE VIEW vw_monthly_dispense_report AS
            SELECT
    i.po_no,
    di.stock_id,
    concat( i.brand_name,' ',
    i.generic_name,' ',
    i.dosage,' ',
    i.dosage_form) as item,
    MONTHNAME(di.transaction_date) AS month_name,
    MONTH(di.transaction_date) AS month_number,
    i.quantity,
     (i.quantity - sum(di.quantity_out)) as balance,
    SUM(di.quantity_out) AS total_dispensed

        FROM
            tbl_daily_inventory di
        JOIN
            tbl_items i ON di.stock_id = i.id
        WHERE
            YEAR(di.transaction_date) = YEAR(CURDATE()) and
            di.status = 'CLOSE'
        GROUP BY
            i.po_no, di.stock_id, month_number, month_name, i.brand_name, i.generic_name, i.dosage, i.dosage_form,i.quantity
        ORDER BY
        month_number
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        DB::statement("DROP VIEW IF EXISTS vw_monthly_report;");
    }
};
