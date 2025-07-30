<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        schema::create('tbl_ris',function(Blueprint $table){
            $table->id();
            $table->date('transaction_date');
            $table->string('purpose');
            $table->string('ris_id');
            $table->unsignedBigInteger('userid');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        schema::dropIfExists('tbl_ris');
    }
};
