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
        Schema::table('transformers', function (Blueprint $table) {
            $table->foreign(['fer_id'])->references(['id'])->on('fermes')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['pro_id'])->references(['id'])->on('produits')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['trans_id'], 'transformers_trans_id_fkey')->references(['id'])->on('transformations')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transformers', function (Blueprint $table) {
            $table->dropForeign('transformers_fer_id_foreign');
            $table->dropForeign('transformers_pro_id_foreign');
            $table->dropForeign('transformers_trans_id_fkey');
        });
    }
};
