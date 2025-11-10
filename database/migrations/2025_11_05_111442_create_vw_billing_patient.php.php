<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        DB::statement('DROP VIEW IF EXISTS vw_patient_billing');
        // Create the view
        DB::statement("
            CREATE VIEW `maifp`.`vw_patient_billing` AS
            SELECT
                p.id AS patient_id,
                p.firstname,
                p.lastname,
                p.middlename,
                p.ext,
                p.birthdate,
                p.contact_number,
                p.age,
                p.gender,
                p.is_not_tagum,
                p.street,
                p.barangay,
                p.city,
                p.province,
                p.purok,
                p.category,
                p.is_pwd,
                p.is_solo,
                t.id AS transaction_id,
                t.transaction_number,
                t.transaction_type,
                t.status,
                t.transaction_date,
                t.transaction_mode,
                t.purpose
            FROM maifp.transactions t
            LEFT JOIN maifp.patient p ON p.id = t.patient_id
            WHERE t.status = 'Billing'
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_patient_billing");
    }
};
