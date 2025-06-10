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
        Schema::create('lib_SysConf', function (Blueprint $table){
            $table-> id();
            $table-> string('normal_color')->nullable();
            $table-> string('low_color')->nullable();
            $table-> string('empty_color')->nullable();
            $table-> integer('low_count')->nullable();
             $table-> integer('days_toExpire')->nullable();
            $table->timestamps();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('lib_SysConf');
    }
};
