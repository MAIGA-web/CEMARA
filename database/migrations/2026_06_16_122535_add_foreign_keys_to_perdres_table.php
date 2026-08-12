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
        Schema::table('perdres', function (Blueprint $table) {
            $table->foreign(['fer_id'])->references(['id'])->on('fermes')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['per_id'])->references(['id'])->on('pertes')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['pro_id'])->references(['id'])->on('produits')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perdres', function (Blueprint $table) {
            $table->dropForeign('perdres_fer_id_foreign');
            $table->dropForeign('perdres_per_id_foreign');
            $table->dropForeign('perdres_pro_id_foreign');
        });
    }
};
