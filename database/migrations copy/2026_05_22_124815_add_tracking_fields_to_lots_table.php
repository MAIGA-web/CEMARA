<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            // Vérifie si ces champs n'existent pas déjà, sinon ajoute-les :
            if (!Schema::hasColumn('lots', 'lot_qte_initiale')) {
                $table->integer('lot_qte_initiale')->default(0);
            }
            if (!Schema::hasColumn('lots', 'lot_date_arrivee')) {
                $table->date('lot_date_arrivee')->default(now());
            }
            if (!Schema::hasColumn('lots', 'lot_date_sortie_prevue')) {
                $table->date('lot_date_sortie_prevue')->nullable();
            }
            if (!Schema::hasColumn('lots', 'lot_actif')) {
                $table->boolean('lot_actif')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->dropColumn(['lot_qte_initiale', 'lot_date_arrivee', 'lot_date_sortie_prevue', 'lot_actif']);
        });
    }
};
