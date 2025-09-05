<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE VIEW vw_patient_medication AS
            SELECT
                p.id AS patient_id,                         -- real patient ID
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
                t.status AS transaction_status,
                t.transaction_date,
                c.id AS consultation_id,
                c.status AS consultation_status
            FROM transaction t
            LEFT JOIN patient p ON p.id = t.patient_id
            LEFT JOIN new_consultation c ON c.transaction_id = t.id
            LEFT JOIN medication m ON m.transaction_id = t.id
            WHERE t.status = 'qualified'
              AND (
                    t.transaction_type = 'Medication'
                    OR c.status = 'Medication'
              )
              AND (
                    m.id IS NULL
                    OR m.status <> 'Done'
              )
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_patient_medication");
    }
};
