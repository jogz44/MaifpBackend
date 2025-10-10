<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {

        DB::statement('DROP VIEW IF EXISTS vw_patient_assessment_philhealth_to_maifip');
        DB::statement(
            "
      CREATE

VIEW `vw_patient_assessment_philhealth_to_maifip` AS
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
        `t`.`status` AS `status`,
        `t`.`philhealth` AS `philhealth`,
        `t`.`transaction_date` AS `transaction_date`,
        `t`.`transaction_mode` AS `transaction_mode`,
        `t`.`purpose` AS `purpose`
    FROM
        (`transaction` `t`
        LEFT JOIN `patient` `p` ON ((`p`.`id` = `t`.`patient_id`)))
    WHERE
        ((`t`.`status` = 'evaluation')
            AND (`t`.`philhealth` = TRUE)
            AND (`t`.`maifip` = TRUE))
        "
        );
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_patient_assessment_philhealth_to_maifip");
    }
};
