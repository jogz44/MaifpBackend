<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_patient_philhealth');

        DB::statement(
            "
           CREATE
VIEW `maifp`.`vw_patient_philhealth` AS
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
        (`maifp`.`patient` `p`
        JOIN `maifp`.`transactions` `t` ON ((`t`.`patient_id` = `p`.`id`)))
    WHERE
        ((`t`.`status` <> 'Funded')
            AND (((`t`.`status` <> 'Complete')
            AND (`t`.`status` <> 'Evaluation')
            AND (`t`.`status` <> 'Billing')
            AND EXISTS( SELECT
                1
            FROM
                `maifp`.`new_consultation` `c`
            WHERE
                ((`c`.`transaction_id` = `t`.`id`)
                    AND (`c`.`status` = 'Done'))))
            OR ((`t`.`status` <> 'Complete')
            AND EXISTS( SELECT
                1
            FROM
                `maifp`.`new_consultation` `c2`
            WHERE
                (`c2`.`transaction_id` = `t`.`id`))
            IS FALSE
            AND EXISTS( SELECT
                1
            FROM
                `maifp`.`laboratory` `l`
            WHERE
                ((`l`.`transaction_id` = `t`.`id`)
                    AND (`l`.`status` = 'Done'))))
            OR ((`t`.`status` <> 'Complete')
            AND EXISTS( SELECT
                1
            FROM
                `maifp`.`medication` `m`
            WHERE
                ((`m`.`transaction_id` = `t`.`id`)
                    AND (`m`.`status` = 'Done'))))))
    ORDER BY `p`.`id`
        "
        );
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_patient_philhealth");
    }
};
