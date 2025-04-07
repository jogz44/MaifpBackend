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
        Schema::create('tbl_items',function (Blueprint $table){
        $table->id();
        $table->string('po_no')->required();
        $table->string('brand_name')->required();
        $table->string('generic_name')->required();
        $table->string('dosage')->required();
        $table->string('dosage_form');
        $table->string('category');
        $table->string('unit')->required();
        $table->double('price');
        $table->double('quantity')->required();
        $table->double('box_quantity');
        $table->double('quantity_per_box');
        $table->date('expiration_date')->required();
        // $table->double('unit_price')->required();
        $table->bigInteger('user_id')->required();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('tbl_items');
    }
};
