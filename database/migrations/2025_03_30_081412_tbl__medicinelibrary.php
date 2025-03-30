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
        Schema::create('tbl_medicinelibrary',function (Blueprint $table){
            $table->id();
            $table->string('brand_name')->required();
            $table->string('generic_name')->required();
            $table->string('dosage')->required();
            $table->string('dosage_form');
            $table->string('category');
            $table->bigInteger('user_id')->required();
            $table->timestamps();
    });

    /**
     * Reverse the migrations.
     */
}
    public function down(): void
    {
        //
        Schema::dropIfExists('tbl_medicinelibrary');
    }
};
