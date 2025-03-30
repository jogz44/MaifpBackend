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
        Schema::create('tbl_daily_inventory', function(Blueprint $table){
            $table -> id();
            $table -> bigInteger('stock_id');
            $table -> double('Openning_quantity');
            $table -> double('Closing_quantity');
            $table -> double('quantity_out');
            $table -> date('transaction_date');
            $table -> bigInteger('user_id');
            $table -> string('remarks');
            $table -> string('status');
            $table -> timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
         Schema::dropIfExists('tbl_daily_inventory');
    }
};
