<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_billing_report');

        DB::statement(
            "
           CREATE

VIEW `vw_billing_report` AS
    SELECT
        `t`.`id` AS `transaction_id`,
        `t`.`patient_id` AS `patient_id`,
        `t`.`transaction_type` AS `transaction_type`,
        `t`.`transaction_date` AS `transaction_date`,
        `t`.`status` AS `status`,
        `p`.`firstname` AS `firstname`,
        `p`.`lastname` AS `lastname`,
        `p`.`middlename` AS `middlename`,
        `p`.`ext` AS `ext`,
        `p`.`birthdate` AS `birthdate`,
        `p`.`age` AS `age`,
        `p`.`contact_number` AS `contact_number`,
        `p`.`barangay` AS `barangay`
    FROM
        (`transactions` `t`
        JOIN `patient` `p` ON ((`t`.`patient_id` = `p`.`id`)))
    WHERE
        (`t`.`status` IN ('Complete' , 'Funded', 'Paid'))
        "
    );
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_billing_report");
    }
};
