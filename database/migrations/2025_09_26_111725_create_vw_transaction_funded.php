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
        // Drop view if it already exists
        DB::statement('DROP VIEW IF EXISTS vw_transaction_funded');

        // Create the view
        DB::statement("
            CREATE VIEW vw_transaction_Funded AS
            SELECT
                t.id AS transaction_id,
                t.patient_id,
                t.transaction_type,
                t.transaction_date,
                t.status,
                p.firstname,
                p.lastname,
                p.middlename,
                p.ext,
                p.birthdate,
                p.age,
                p.contact_number,
                p.barangay
            FROM transaction t
            JOIN patient p ON t.patient_id = p.id
            WHERE t.status = 'Funded'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_transaction_Funded');
    }
};
