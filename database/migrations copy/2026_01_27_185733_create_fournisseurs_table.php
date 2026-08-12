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
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('four_nom');
            $table->string('four_prenom');
            $table->string('four_adresse');
            $table->string('four_sexe');
            $table->string('four_tel')->unique();
            $table->boolean('four_etat')->default(true);
    $table->foreignId('fer_id')->constrained('fermes')->onDelete('cascade');

            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fournisseurs');
    }
};
