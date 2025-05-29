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
        CREATE OR REPLACE VIEW vw_customers_registered AS
        SELECT COUNT(*) as Total_Customers  FROM tbl_customers;
        ");

        //     SELECT COUNT(*) as Total_Customers,
        //        SUM(CASE WHEN created_at >= CURDATE() THEN 1 ELSE 0 END) AS Customers_Registered_Today,
        //        SUM(CASE WHEN created_at < CURDATE() THEN 1 ELSE 0 END) AS Customers_Registered_Previous
        // FROM tbl_customers;
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        DB::statement("DROP VIEW IF EXISTS vw_customers_registered;");
    }
};
