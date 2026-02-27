<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_patient_medication_new');

        DB::statement("
        CREATE VIEW vw_patient_medication_new AS
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
            t.status AS transaction_status,
            t.transaction_date,
            t.created_at AS transaction_created_at,
            t.updated_at AS transaction_updated_at,

            c.id AS consultation_id,
            c.status AS consultation_status,
            c.created_at AS consultation_created_at,
            c.updated_at AS consultation_updated_at

        FROM transactions t
        LEFT JOIN patient p ON p.id = t.patient_id
        LEFT JOIN new_consultation c ON c.transaction_id = t.id

        WHERE
            -- ✅ Transaction Status
            t.status IN ('Qualified','Pending')

            -- ✅ Transaction Type OR Consultation Status
            AND (
                t.transaction_type IN ('Laboratory','Consultation','Medication')
                OR EXISTS (
                    SELECT 1
                    FROM new_consultation c2
                    WHERE c2.transaction_id = t.id
                    AND c2.status IN ('Processing','Returned','Medication')
                )
            )

            -- ✅ No Medication with status Done
            AND NOT EXISTS (
                SELECT 1
                FROM medication m
                WHERE m.transaction_id = t.id
                AND m.status = 'Done'
            )

            -- ✅ No Laboratory with status Done or Pending
            AND NOT EXISTS (
                SELECT 1
                FROM laboratory l
                WHERE l.transaction_id = t.id
                AND l.status IN ('Done','Pending')
            )
    ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_patient_medication_new");
    }
};
