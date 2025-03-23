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
        Schema::create('tbl_customers',function (Blueprint $table){

            $table->id();
            $table-> string('firstname')->required();
            $table-> string('lastname')->required();
            $table-> string('middlename')->nullable();
            $table-> string('ext')->nullable();
            $table-> date('birthdate');
            $table-> string('contact_number');
            $table-> integer('age');
            $table-> string('gender');
            $table-> boolean('is_not_tagum')->default(false);
            $table-> string('street');
            $table-> string('purok');
            $table-> string('barangay');
            $table-> string('city');
            $table-> string('province');
            $table-> string('category');
            $table -> boolean('is_pwd') -> default(false);
            $table -> boolean('is_solo') -> default(false);
            $table -> bigInteger('user_id');
            $table->timestamps();


        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('tbl_customers');
    }
};
