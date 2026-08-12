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
        Schema::table('lots', function (Blueprint $table) {
            $table->foreign(['fer_id'])->references(['id'])->on('fermes')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['poul_id'])->references(['id'])->on('poulaillers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['pro_id'])->references(['id'])->on('produits')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->dropForeign('lots_fer_id_foreign');
            $table->dropForeign('lots_poul_id_foreign');
            $table->dropForeign('lots_pro_id_foreign');
        });
    }
};
