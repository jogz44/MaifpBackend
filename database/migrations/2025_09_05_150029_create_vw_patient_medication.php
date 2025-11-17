<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_patient_medication');
        DB::statement("
           CREATE

    VIEW `maifp`.`vw_patient_medication` AS
    SELECT
        `p`.`id` AS `patient_id`,
        `p`.`firstname` AS `firstname`,
        `p`.`lastname` AS `lastname`,
        `p`.`middlename` AS `middlename`,
        `p`.`ext` AS `ext`,
        `p`.`birthdate` AS `birthdate`,
        `p`.`contact_number` AS `contact_number`,
        `p`.`age` AS `age`,
        `p`.`gender` AS `gender`,
        `p`.`is_not_tagum` AS `is_not_tagum`,
        `p`.`street` AS `street`,
        `p`.`barangay` AS `barangay`,
        `p`.`city` AS `city`,
        `p`.`province` AS `province`,
        `p`.`purok` AS `purok`,
        `p`.`category` AS `category`,
        `p`.`is_pwd` AS `is_pwd`,
        `p`.`is_solo` AS `is_solo`,
        `t`.`id` AS `transaction_id`,
        `t`.`transaction_number` AS `transaction_number`,
        `t`.`transaction_type` AS `transaction_type`,
        `t`.`status` AS `transaction_status`,
        `t`.`transaction_date` AS `transaction_date`,
        `t`.`created_at` AS `transaction_created_at`,
        `t`.`updated_at` AS `transaction_updated_at`,
        `c`.`id` AS `consultation_id`,
        `c`.`status` AS `consultation_status`,
        `c`.`created_at` AS `consultation_created_at`,
        `c`.`updated_at` AS `consultation_updated_at`
    FROM
        (((`maifp`.`transactions` `t`
        LEFT JOIN `maifp`.`patient` `p` ON ((`p`.`id` = `t`.`patient_id`)))
        LEFT JOIN `maifp`.`new_consultation` `c` ON ((`c`.`transaction_id` = `t`.`id`)))
        LEFT JOIN `maifp`.`medication` `m` ON ((`m`.`transaction_id` = `t`.`id`)))
    WHERE
        ((`t`.`status` IN ('Qualified' , 'Pending'))
            AND ((`t`.`transaction_type` = 'Medication')
            OR (`c`.`status` = 'Medication'))
            AND ((`m`.`id` IS NULL)
            OR (`m`.`status` <> 'Done')))
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_patient_medication");
    }
};
