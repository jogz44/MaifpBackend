<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        DB::statement('DROP VIEW IF EXISTS vw_patient_billing');
        // Create the view
        DB::statement("
          CREATE
VIEW `maifp`.`vw_patient_billing` AS
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
        `t`.`transaction_mode` AS `transaction_mode`,
        `t`.`purpose` AS `purpose`,
        `nc`.`status` AS `consultation_status`,
        `nc`.`amount` AS `consultation_amount`,
        (((((IFNULL(`nc`.`amount`, 0) + IFNULL((SELECT
                        SUM(`maifp`.`lab_examination_details`.`total_amount`)
                    FROM
                        `maifp`.`lab_examination_details`
                    WHERE
                        (`maifp`.`lab_examination_details`.`transaction_id` = `t`.`id`)),
                0)) + IFNULL((SELECT
                        SUM(`maifp`.`lab_mammogram_details`.`total_amount`)
                    FROM
                        `maifp`.`lab_mammogram_details`
                    WHERE
                        (`maifp`.`lab_mammogram_details`.`transaction_id` = `t`.`id`)),
                0)) + IFNULL((SELECT
                        SUM(`maifp`.`lab_radiology_details`.`total_amount`)
                    FROM
                        `maifp`.`lab_radiology_details`
                    WHERE
                        (`maifp`.`lab_radiology_details`.`transaction_id` = `t`.`id`)),
                0)) + IFNULL((SELECT
                        SUM(`maifp`.`lab_ultrasound_details`.`total_amount`)
                    FROM
                        `maifp`.`lab_ultrasound_details`
                    WHERE
                        (`maifp`.`lab_ultrasound_details`.`transaction_id` = `t`.`id`)),
                0)) + IFNULL((SELECT
                        SUM(`maifp`.`medication_details`.`amount`)
                    FROM
                        `maifp`.`medication_details`
                    WHERE
                        (`maifp`.`medication_details`.`transaction_id` = `t`.`id`)),
                0)) AS `total_service_amount`
    FROM
        ((`maifp`.`transactions` `t`
        LEFT JOIN `maifp`.`patient` `p` ON ((`p`.`id` = `t`.`patient_id`)))
        LEFT JOIN `maifp`.`new_consultation` `nc` ON ((`nc`.`transaction_id` = `t`.`id`)))
    WHERE
        ((`t`.`status` NOT IN ('GL' , 'Funded',
            'Complete',
            'Paid',
            'Evaluation',
            'Assessment'))
            AND (((`t`.`status` IN ('Billing' , 'Expired'))
            AND ((((((IFNULL(`nc`.`amount`, 0) + IFNULL((SELECT
                        SUM(`maifp`.`lab_examination_details`.`total_amount`)
                    FROM
                        `maifp`.`lab_examination_details`
                    WHERE
                        (`maifp`.`lab_examination_details`.`transaction_id` = `t`.`id`)),
                0)) + IFNULL((SELECT
                        SUM(`maifp`.`lab_mammogram_details`.`total_amount`)
                    FROM
                        `maifp`.`lab_mammogram_details`
                    WHERE
                        (`maifp`.`lab_mammogram_details`.`transaction_id` = `t`.`id`)),
                0)) + IFNULL((SELECT
                        SUM(`maifp`.`lab_radiology_details`.`total_amount`)
                    FROM
                        `maifp`.`lab_radiology_details`
                    WHERE
                        (`maifp`.`lab_radiology_details`.`transaction_id` = `t`.`id`)),
                0)) + IFNULL((SELECT
                        SUM(`maifp`.`lab_ultrasound_details`.`total_amount`)
                    FROM
                        `maifp`.`lab_ultrasound_details`
                    WHERE
                        (`maifp`.`lab_ultrasound_details`.`transaction_id` = `t`.`id`)),
                0)) + IFNULL((SELECT
                        SUM(`maifp`.`medication_details`.`amount`)
                    FROM
                        `maifp`.`medication_details`
                    WHERE
                        (`maifp`.`medication_details`.`transaction_id` = `t`.`id`)),
                0)) > 0))
            OR ((`nc`.`status` IN ('Processing' , 'Returned', 'Medication', 'Done'))
            AND ((((((IFNULL(`nc`.`amount`, 0) + IFNULL((SELECT
                        SUM(`maifp`.`lab_examination_details`.`total_amount`)
                    FROM
                        `maifp`.`lab_examination_details`
                    WHERE
                        (`maifp`.`lab_examination_details`.`transaction_id` = `t`.`id`)),
                0)) + IFNULL((SELECT
                        SUM(`maifp`.`lab_mammogram_details`.`total_amount`)
                    FROM
                        `maifp`.`lab_mammogram_details`
                    WHERE
                        (`maifp`.`lab_mammogram_details`.`transaction_id` = `t`.`id`)),
                0)) + IFNULL((SELECT
                        SUM(`maifp`.`lab_radiology_details`.`total_amount`)
                    FROM
                        `maifp`.`lab_radiology_details`
                    WHERE
                        (`maifp`.`lab_radiology_details`.`transaction_id` = `t`.`id`)),
                0)) + IFNULL((SELECT
                        SUM(`maifp`.`lab_ultrasound_details`.`total_amount`)
                    FROM
                        `maifp`.`lab_ultrasound_details`
                    WHERE
                        (`maifp`.`lab_ultrasound_details`.`transaction_id` = `t`.`id`)),
                0)) + IFNULL((SELECT
                        SUM(`maifp`.`medication_details`.`amount`)
                    FROM
                        `maifp`.`medication_details`
                    WHERE
                        (`maifp`.`medication_details`.`transaction_id` = `t`.`id`)),
                0)) > 0))))
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_patient_billing");
    }
};
