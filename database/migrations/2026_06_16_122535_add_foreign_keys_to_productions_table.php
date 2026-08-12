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
        Schema::table('productions', function (Blueprint $table) {
            $table->foreign(['fer_id'])->references(['id'])->on('fermes')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['poul_id'])->references(['id'])->on('poulaillers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->dropForeign('productions_fer_id_foreign');
            $table->dropForeign('productions_poul_id_foreign');
        });
    }
};
