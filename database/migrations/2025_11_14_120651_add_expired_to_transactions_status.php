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
        DB::statement("
            ALTER TABLE `transactions`
            MODIFY `status` ENUM(
                'Pending',
                'Qualified',
                'Unqualified',
                'Assessment',
                'Complete',
                'Funded',
                'Evaluation',
                'Billing',
                'Paid',
                'not started',
                'Expired'
            ) DEFAULT 'not started';
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE `transactions`
            MODIFY `status` ENUM(
                'Pending',
                'Qualified',
                'Unqualified',
                'Assessment',
                'Complete',
                'Funded',
                'Evaluation',
                'Billing',
                'Paid',
                'not started'
            ) DEFAULT 'not started';
        ");
    }
};
