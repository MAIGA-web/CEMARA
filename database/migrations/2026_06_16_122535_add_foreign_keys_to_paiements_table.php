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
        Schema::table('paiements', function (Blueprint $table) {
            $table->foreign(['fer_id'])->references(['id'])->on('fermes')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['mod_id'])->references(['id'])->on('modes')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['vte_id'])->references(['id'])->on('ventes')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropForeign('paiements_fer_id_foreign');
            $table->dropForeign('paiements_mod_id_foreign');
            $table->dropForeign('paiements_vte_id_foreign');
        });
    }
};
