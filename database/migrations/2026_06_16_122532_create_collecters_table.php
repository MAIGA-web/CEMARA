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
        Schema::create('collecters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('qte_ramasse')->default(0);
            $table->integer('qte_casse')->default(0);
            $table->integer('qte_consomme')->default(0);
            $table->bigInteger('col_id');
            $table->bigInteger('fer_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collecters');
    }
};
