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
            $table->string('gl_number')->nullable()->after('id');
            $table->json('laboratories_details')->nullable()->after('transaction_id');
            $table->json('medication')->nullable()->after('laboratories_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assistances', function (Blueprint $table) {
            //
        });
    }
};
