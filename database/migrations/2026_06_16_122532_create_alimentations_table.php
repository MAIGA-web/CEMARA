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
        Schema::create('alimentations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('alm_etat')->default(true);
            $table->bigInteger('poul_id');
            $table->bigInteger('fer_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alimentations');
    }
};
