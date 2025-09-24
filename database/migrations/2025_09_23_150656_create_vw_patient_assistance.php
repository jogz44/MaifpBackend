<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(
            "
        CREATE VIEW patient_assistance_funds AS
            SELECT
                p.id AS patient_id,
                CONCAT(p.firstname, ' ', p.lastname) AS patient_name,
                t.id AS transaction_id,
                t.transaction_date,
                af.fund_source,
                af.fund_amount
            FROM assistances_funds af
            INNER JOIN assistances a ON af.assistance_id = a.id
            INNER JOIN patient p ON a.patient_id = p.id
            INNER JOIN transaction t ON a.transaction_id = t.id;
        "
    );
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS patient_assistance_funds");
    }
};
