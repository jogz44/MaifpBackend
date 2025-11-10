<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_patient_assessment_maifip');

        DB::statement("
            CREATE VIEW vw_patient_assessment_maifip AS
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
                t.id,
                t.transaction_number,
                t.transaction_type,
                t.status,
                t.transaction_date,
                t.transaction_mode,
                t.purpose
            FROM transactions t
            LEFT JOIN patient p ON p.id = t.patient_id
           WHERE
        (`t`.`status` IN ('Assessment' , 'Evaluation'))
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_patient_assessment_maifip");
    }
};
