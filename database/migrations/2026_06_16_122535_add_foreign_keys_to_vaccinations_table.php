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
        Schema::table('vaccinations', function (Blueprint $table) {
            $table->foreign(['fer_id'])->references(['id'])->on('fermes')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['poul_id'])->references(['id'])->on('poulaillers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['pro_id'])->references(['id'])->on('produits')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['vtr_id'])->references(['id'])->on('veterinaires')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vaccinations', function (Blueprint $table) {
            $table->dropForeign('vaccinations_fer_id_foreign');
            $table->dropForeign('vaccinations_poul_id_foreign');
            $table->dropForeign('vaccinations_pro_id_foreign');
            $table->dropForeign('vaccinations_vtr_id_foreign');
        });
    }
};
