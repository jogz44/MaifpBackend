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
        // ⚠️ Note: Directly altering ENUM values via Laravel schema is not supported,
        // so we use raw SQL instead.
        DB::statement("ALTER TABLE `transactions`
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
            ) DEFAULT 'not started'");
    }

    public function down(): void
    {
        // Rollback to original list (without 'Paid')
        DB::statement("ALTER TABLE `transactions`
            MODIFY `status` ENUM(
                'Pending',
                'Qualified',
                'Unqualified',
                'Assessment',
                'Complete',
                'Funded',
                'Evaluation',
                'Billing',
                'not started'
            ) DEFAULT 'not started'");
    }
};
