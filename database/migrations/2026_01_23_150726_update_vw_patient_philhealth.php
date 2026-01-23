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
        DB::statement('DROP VIEW IF EXISTS vw_patient_philhealth');

        DB::statement("
        CREATE VIEW vw_patient_philhealth AS
        SELECT DISTINCT
            p.id AS patient_id,
            p.firstname,
            p.lastname,
            p.middlename,
            p.ext,
            p.birthdate,
            p.age,
            p.contact_number,
            p.barangay,
            t.id AS transaction_id,
            t.transaction_number,
            t.transaction_type,
            t.status AS transaction_status,
            t.transaction_date,
            t.transaction_mode,
            t.purpose,
            t.created_at AS transaction_created_at,
            t.updated_at AS transaction_updated_at
        FROM patient p
        JOIN transactions t ON t.patient_id = p.id
        WHERE
            t.status NOT IN ('Funded','Complete','Evaluation','Billing','Paid')
        AND (
            EXISTS (
                SELECT 1 FROM new_consultation c
                WHERE c.transaction_id = t.id
                AND c.status = 'Done'
            )
            OR (
                NOT EXISTS (
                    SELECT 1 FROM new_consultation c2
                    WHERE c2.transaction_id = t.id
                )
                AND EXISTS (
                    SELECT 1 FROM laboratory l
                    WHERE l.transaction_id = t.id
                    AND l.status = 'Done'
                )
            )
            OR EXISTS (
                SELECT 1 FROM medication m
                WHERE m.transaction_id = t.id
                AND m.status = 'Done'
            )
        )
        ORDER BY p.id
    ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_patient_philhealth');
    }
};
