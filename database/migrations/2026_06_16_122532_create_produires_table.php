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
        Schema::create('produires', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('prdr_qte');
            $table->bigInteger('prodc_id');
            $table->bigInteger('pro_id');
            $table->bigInteger('fer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produires');
    }
};
