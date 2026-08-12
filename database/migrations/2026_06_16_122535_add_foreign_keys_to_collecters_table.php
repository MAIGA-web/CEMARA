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
        Schema::table('collecters', function (Blueprint $table) {
            $table->foreign(['col_id'])->references(['id'])->on('collections')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['fer_id'])->references(['id'])->on('fermes')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collecters', function (Blueprint $table) {
            $table->dropForeign('collecters_col_id_foreign');
            $table->dropForeign('collecters_fer_id_foreign');
        });
    }
};
