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
        Schema::create('lots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('origine');
            $table->bigInteger('poul_id');
            $table->bigInteger('fer_id');
            $table->timestamps();
            $table->integer('lot_qte_initiale')->default(0);
            $table->date('lot_date_arrivee')->default('2026-05-22');
            $table->date('lot_date_sortie_prevue')->nullable();
            $table->boolean('lot_actif')->default(true);
            $table->string('lot_code', 30);
            $table->bigInteger('pro_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};
