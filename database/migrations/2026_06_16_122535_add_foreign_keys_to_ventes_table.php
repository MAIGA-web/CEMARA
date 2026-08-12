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
        Schema::table('ventes', function (Blueprint $table) {
            $table->foreign(['cl_id'])->references(['id'])->on('clients')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['fer_id'])->references(['id'])->on('fermes')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->dropForeign('ventes_cl_id_foreign');
            $table->dropForeign('ventes_fer_id_foreign');
        });
    }
};
