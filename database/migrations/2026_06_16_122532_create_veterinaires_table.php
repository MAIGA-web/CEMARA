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
        Schema::create('veterinaires', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('vtr_nom');
            $table->string('vtr_prenom');
            $table->string('vtr_adresse');
            $table->string('vtr_sexe');
            $table->string('vtr_tel')->unique();
            $table->boolean('vtr_etat')->default(true);
            $table->bigInteger('fer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veterinaires');
    }
};
