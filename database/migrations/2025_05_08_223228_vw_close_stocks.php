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
        DB::statement("
        CREATE OR REPLACE VIEW vw_inventory_close_list AS
        SELECT
            inv.stock_id,
            items.po_no,
            items.brand_name,
            items.generic_name,
            items.dosage,
            items.dosage_form,
            items.unit,
            items.price_per_pcs,
            items.quantity,
            inv.Closing_quantity AS total_closing_quantity,
            inv.Openning_quantity AS total_openning_quantity,
            items.box_quantity,
            items.quantity_per_box,
            items.expiration_date,
            inv.status as stock_status,
            inv.transaction_date
        FROM tbl_daily_inventory inv
        JOIN tbl_items items ON inv.stock_id = items.id
        GROUP BY
            items.po_no,
            inv.stock_id,
            items.brand_name,
            items.generic_name,
            items.dosage_form,
            items.dosage,
            items.unit,
            items.price,
            items.price_per_pcs,
            items.quantity,
            items.box_quantity,
            items.quantity_per_box,
            items.expiration_date,
            stock_status,
            total_closing_quantity,
            total_openning_quantity,
            inv.transaction_date;
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        // Drop the view if it exists
        DB::statement("DROP VIEW IF EXISTS vw_inventory_close_list");
    }
};
