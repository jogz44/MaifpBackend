<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_patient_billing');

        DB::statement("
            CREATE VIEW `vw_patient_billing` AS
            SELECT DISTINCT
                `p`.`id` AS `patient_id`,
                `p`.`firstname` AS `firstname`,
                `p`.`lastname` AS `lastname`,
                `p`.`middlename` AS `middlename`,
                `p`.`ext` AS `ext`,
                `p`.`birthdate` AS `birthdate`,
                `p`.`age` AS `age`,
                `p`.`contact_number` AS `contact_number`,
                `p`.`barangay` AS `barangay`,
                `t`.`id` AS `transaction_id`,
                `t`.`transaction_number` AS `transaction_number`,
                `t`.`transaction_type` AS `transaction_type`,
                `t`.`status` AS `transaction_status`,
                `t`.`transaction_date` AS `transaction_date`,
                `t`.`transaction_mode` AS `transaction_mode`,
                `t`.`purpose` AS `purpose`,
                `t`.`created_at` AS `transaction_created_at`,
                `t`.`updated_at` AS `transaction_updated_at`
              FROM
                (`maifp`.`transactions` `t`
                     LEFT JOIN `maifp`.`patient` `p` ON ((`p`.`id` = `t`.`patient_id`)))
                  WHERE
                (`t`.`status` = 'Billing')
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_patient_billing");
    }
};
