<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    // public function up(): void
    // {
    //     DB::statement("
    //         ALTER TABLE transactions
    //         MODIFY COLUMN status
    //         ENUM('qualified', 'unqualified', 'assessment', 'Complete', 'Funded', 'evaluation')
    //         DEFAULT 'assessment'
    //     ");
    // }

    // public function down(): void
    // {
    //     // Revert back to the previous ENUM without 'evaluation'
    //     DB::statement("
    //         ALTER TABLE transaction
    //         MODIFY COLUMN status
    //         ENUM('qualified', 'unqualified', 'assessment', 'Complete', 'Funded')
    //         DEFAULT 'assessment'
    //     ");
    // }
};
