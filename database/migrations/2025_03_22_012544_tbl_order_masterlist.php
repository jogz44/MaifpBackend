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
        Schema::create('tbl_daily_transactions',function (Blueprint $table){
            $table -> id();
            $table -> string('transaction_id')->required();
            $table -> bigInteger('item_id')->required();
            $table -> bigInteger('customer_id')->required();
            $table -> bigInteger('quantity')->required();
            $table -> date('transaction_date')->required();
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
        Schema::dropIfExists('tbl_daily_transactions');
    }
};
