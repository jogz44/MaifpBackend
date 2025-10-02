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
        Schema::table('laboratory', function (Blueprint $table) {
            //
            $table->foreignId('new_consultation_id')->after('transaction_id')->nullable()->constrained('new_consultation')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratory', function (Blueprint $table) {
            //
            // first drop the foreign key
            $table->dropForeign(['new_consultation_id']);
            // then drop the column
            $table->dropColumn('new_consultation_id');
        });
    }
};
