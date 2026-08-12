<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            // Associe le lot à un produit (ex: Poussins)
            $table->foreignId('pro_id')
                  ->nullable() // Nécessaire si tu as déjà des données pour éviter les conflits au déploiement
                  ->constrained('produits')
                  ->onDelete('set null'); 
        });
    }

    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->dropForeign(['pro_id']);
            $table->dropColumn('pro_id');
        });
    }
};