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
        Schema::table('assistances', function (Blueprint $table) {
            //
            $table->json('ultrasound_details')->nullable()->after('transaction_id');
            $table->json('examination_details')->nullable()->after('ultrasound_details');
            $table->json('radiology_details')->nullable()->after('examination_details');
            $table->json('mammogram_details')->nullable()->after('radiology_details');

            $table->decimal('radiology_total', 15, 2)->default(0)->after('medication');
            $table->decimal('examination_total', 15, 2)->default(0)->after('radiology_total');
            $table->decimal('mammogram_total', 15, 2)->default(0)->after('examination_total');
            $table->decimal('ultrasound_total', 15, 2)->default(0)->after('mammogram_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assistances', function (Blueprint $table) {
            // Drop the JSON columns
            $table->dropColumn('ultrasound_details');
            $table->dropColumn('examination_details');
            $table->dropColumn('radiology_details');
            $table->dropColumn('mammogram_details');

            // Drop the decimal columns
            $table->dropColumn('radiology_total');
            $table->dropColumn('examination_total');
            $table->dropColumn('mammogram_total');
            $table->dropColumn('ultrasound_total');
        });
    }
};
