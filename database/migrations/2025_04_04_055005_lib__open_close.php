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
        //

        Schema::create('tbl_libIndicator',function (Blueprint $table){
            $table->id();
            $table-> boolean('is_open')->default(false);
            $table-> boolean('is_close')->default(false);
            $table-> date('transaction_date');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('tbl_libIndicator');
    }
};
