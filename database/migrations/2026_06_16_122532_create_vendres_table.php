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
        Schema::create('vendres', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('vdr_pu', 10);
            $table->integer('vdr_qte');
            $table->bigInteger('vte_id');
            $table->bigInteger('fer_id');
            $table->bigInteger('pro_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendres');
    }
};
