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
        Schema::create('perdres', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('perd_qte');
            $table->string('motif');
            $table->bigInteger('pro_id');
            $table->bigInteger('per_id');
            $table->bigInteger('fer_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perdres');
    }
};
