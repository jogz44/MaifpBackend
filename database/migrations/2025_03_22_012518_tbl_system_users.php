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
        Schema::create('tbl_system_users',function (Blueprint $table){
            $table -> id();
            $table -> string('first_name')->required();
            $table -> string('middle_name')->nullable();
            $table -> string('last_name')->required();
            $table -> string('position')->nullable();
            $table -> string('role')->required();
            $table -> string('user_name')->required();
            $table -> string('password')->required();
            $table -> bigInteger('user_id');
            $table -> timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('tbl_system_users');
    }
};
