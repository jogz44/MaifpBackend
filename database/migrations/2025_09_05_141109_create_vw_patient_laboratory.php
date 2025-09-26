<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        DB::statement('DROP VIEW IF EXISTS vw_patient_laboratory');
        DB::statement("
               CREATE VIEW vw_patient_laboratory AS
            SELECT
                -- Patient fields
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
                p.purok,
                p.barangay,
                p.city,
                p.province,
                p.category,
                p.is_pwd,
                p.is_solo,
                p.user_id,
                p.created_at AS patient_created_at,
                p.updated_at AS patient_updated_at,

                -- Transaction fields
                t.id AS transaction_id,
                t.transaction_number,
                t.transaction_type,
                t.status AS transaction_status,
                t.transaction_date,
                t.transaction_mode,
                t.purpose,
                t.created_at AS transaction_created_at,
                t.updated_at AS transaction_updated_at,
                t.representative_id,

                -- Consultation fields
                c.id AS consultation_id,
                c.status AS consultation_status,

                -- Vital fields
                v.id AS vital_id,
                v.height,
                v.weight,
                v.bmi,
                v.pulse_rate,
                v.temperature,
                v.sp02,
                v.heart_rate,
                v.blood_pressure,
                v.respiratory_rate,
                v.medicine,
                v.LMP


            FROM transaction t
            JOIN patient p ON p.id = t.patient_id
            LEFT JOIN vital v ON v.transaction_id = t.id
            LEFT JOIN new_consultation c ON c.transaction_id = t.id
            LEFT JOIN laboratory l ON l.transaction_id = t.id
            WHERE t.status = 'qualified'
              AND (
                t.transaction_type = 'Laboratory'
                OR c.status = 'Processing'
              )
              AND NOT EXISTS (
                SELECT 1
                FROM laboratory l2
                WHERE l2.transaction_id = t.id
                  AND l2.status IN ('Done','Returned','Pending')
              )
            ORDER BY p.id;
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_patient_laboratory");
    }
};
