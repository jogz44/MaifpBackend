<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
           CREATE OR REPLACE VIEW vw_dailyinventoryinfo AS
SELECT
    d_i.id AS id,
    d_i.stock_id AS stock_id,
    d_i.Openning_quantity AS Openning_quantity,
    d_i.Closing_quantity AS Closing_quantity,
    d_i.quantity_out AS quantity_out,
    d_i.transaction_date AS transaction_date,
    d_i.remarks AS remarks,
    d_i.status AS status,

    i.po_no AS po_no,
    i.brand_name AS brand_name,
    i.generic_name AS generic_name,
    i.dosage AS dosage,
    i.dosage_form AS dosage_form,
    i.category AS category,
    i.unit AS unit,
    i.price AS price,
    i.quantity AS quantity,
    i.box_quantity AS box_quantity,
    i.quantity_per_box AS quantity_per_box,
    i.expiration_date AS expiration_date

FROM
    tbl_daily_inventory AS d_i
JOIN
    tbl_items AS i ON i.id = d_i.stock_id;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        DB::statement("DROP VIEW IF EXISTS vw_dailyinventoryinfo;");
    }
};
