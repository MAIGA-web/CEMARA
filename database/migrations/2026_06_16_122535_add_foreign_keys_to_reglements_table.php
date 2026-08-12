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
        Schema::table('reglements', function (Blueprint $table) {
            $table->foreign(['ac_id'])->references(['id'])->on('achats')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['fer_id'])->references(['id'])->on('fermes')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['mod_id'])->references(['id'])->on('modes')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reglements', function (Blueprint $table) {
            $table->dropForeign('reglements_ac_id_foreign');
            $table->dropForeign('reglements_fer_id_foreign');
            $table->dropForeign('reglements_mod_id_foreign');
        });
    }
};
