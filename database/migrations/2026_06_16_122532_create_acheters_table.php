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
        Schema::create('acheters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('act_qte');
            $table->decimal('act_pu', 10);
            $table->bigInteger('pro_id');
            $table->bigInteger('ac_id');
            $table->bigInteger('fer_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acheters');
    }
};
