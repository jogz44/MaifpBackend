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
        CREATE OR REPLACE VIEW vw_item_info AS
        SELECT DISTINCT
            brand_name,
            generic_name,
            dosage,
            dosage_form
        FROM TBL_ITEMS;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        DB::statement("DROP VIEW IF EXISTS vw_item_info");
    }
};
