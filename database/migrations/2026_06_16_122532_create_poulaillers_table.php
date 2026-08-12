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
        Schema::create('poulaillers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('poul_nom');
            $table->integer('poul_capa');
            $table->string('poul_empl');
            $table->bigInteger('fer_id');
            $table->smallInteger('poul_etat')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poulaillers');
    }
};
