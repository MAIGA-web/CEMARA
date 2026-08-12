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
        Schema::table('vendres', function (Blueprint $table) {
            $table->foreign(['fer_id'])->references(['id'])->on('fermes')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['pro_id'])->references(['id'])->on('produits')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['vte_id'])->references(['id'])->on('ventes')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendres', function (Blueprint $table) {
            $table->dropForeign('vendres_fer_id_foreign');
            $table->dropForeign('vendres_pro_id_foreign');
            $table->dropForeign('vendres_vte_id_foreign');
        });
    }
};
