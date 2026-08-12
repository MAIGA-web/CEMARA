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
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cl_nom');
            $table->string('cl_prenom');
            $table->string('cl_adresse');
            $table->string('cl_sexe');
            $table->string('cl_tel')->unique();
            $table->boolean('cl_etat')->default(true);
            $table->bigInteger('fer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
