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
        Schema::table('patient', function (Blueprint $table) {
            //
            $table->string('philsys_id')->nullable()->after('category');
            $table->string('philhealth_id')->nullable()->after('philsys_id');
            $table->string('place_of_birth')->nullable()->after('philhealth_id');
            $table->string('civil_status')->nullable()->after('place_of_birth');
            $table->string('religion')->nullable()->after('civil_status');
            $table->string('education')->nullable()->after('religion');
            $table->string('occupation')->nullable()->after('education');
            $table->string('income')->nullable()->after('occupation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient', function (Blueprint $table) {
            //

            $table->dropColumn(['philsys_id', 'philhealth_id','place_of_birth','civil_status','religion','education', 'occupation','income']);
        });
    }
};
