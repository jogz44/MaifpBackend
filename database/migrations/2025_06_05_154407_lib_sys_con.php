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
            $table-> string('normal_color');
            $table-> string('low_color');
            $table-> string('empty_color');
            $table-> integer('low_count');
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
