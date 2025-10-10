<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {

        DB::statement('DROP VIEW IF EXISTS vw_fund_sources_summary');
        DB::statement(
            "
            CREATE
            VIEW `vw_fund_sources_summary` AS
                SELECT
                    `assistances_funds`.`fund_source` AS `fund_source`,
                    SUM(`assistances_funds`.`fund_amount`) AS `total_amount`,
                    COUNT(0) AS `patient_count`
                FROM
                    `assistances_funds`
                GROUP BY `assistances_funds`.`fund_source`
        "
        );
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_fund_sources_summary");
    }
};
