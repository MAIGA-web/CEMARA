<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Si tu n'as pas du tout cette table, on la crée
        if (!Schema::hasTable('suivi_journaliers')) {
            Schema::create('suivi_journaliers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fer_id')->constrained('fermes')->onDelete('cascade');
                $table->foreignId('lot_id')->constrained('lots')->onDelete('cascade');
                $table->date('suivi_date');
                $table->integer('morts_jour')->default(0);
                $table->decimal('consommation_aliment', 8, 2)->default(0);
                $table->string('etat_sante')->default('Sain');
                $table->text('observations')->nullable();
                $table->timestamps();
            });
        } else {
            // Si elle existe déjà, on lui ajoute juste les colonnes manquantes
            Schema::table('suivi_journaliers', function (Blueprint $table) {
                if (!Schema::hasColumn('suivi_journaliers', 'lot_id')) {
                    $table->foreignId('lot_id')->constrained('lots')->onDelete('cascade');
                }
                if (!Schema::hasColumn('suivi_journaliers', 'morts_jour')) {
                    $table->integer('morts_jour')->default(0);
                }
                if (!Schema::hasColumn('suivi_journaliers', 'consommation_aliment')) {
                    $table->decimal('consommation_aliment', 8, 2)->default(0);
                }
                if (!Schema::hasColumn('suivi_journaliers', 'etat_sante')) {
                    $table->string('etat_sante')->default('Sain');
                }
            });
        }
    }

    public function down(): void
    {
        // Optionnel : ne rien faire ou drop selon tes besoins de production
    }
};