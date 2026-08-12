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
        Schema::create('suivi_lots', function (Blueprint $table) {
            $table->id();
            $table->integer('qte_perdu')->check('qte_perdu >=0');
            $table->string('description');
            $table->string('etat_sante');
    $table->foreignId('fer_id')->constrained('fermes')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suivi_lots');
    }
};
