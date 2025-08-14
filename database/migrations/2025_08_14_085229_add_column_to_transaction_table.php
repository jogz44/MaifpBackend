<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->string('transaction_mode')->nullable()->after('transaction_date');
            $table->string('purpose')->nullable()->after('transaction_mode');

            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            //
            $table->dropColumn('transaction_mode');
            $table->dropColumn('purpose');
        });
    }
};
