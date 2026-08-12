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
        Schema::table('produires', function (Blueprint $table) {
            $table->foreign(['fer_id'])->references(['id'])->on('fermes')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['pro_id'])->references(['id'])->on('produits')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['prodc_id'])->references(['id'])->on('productions')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produires', function (Blueprint $table) {
            $table->dropForeign('produires_fer_id_foreign');
            $table->dropForeign('produires_pro_id_foreign');
            $table->dropForeign('produires_prodc_id_foreign');
        });
    }
};
