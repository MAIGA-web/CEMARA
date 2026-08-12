<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collecters', function (Blueprint $table) {
            $table->id();
            $table->integer('qte_ramasse')->default(0);
            $table->integer('qte_casse')->default(0);
            $table->integer('qte_consomme')->default(0);
            $table->unsignedBigInteger('col_id'); // Clé étrangère vers la fiche maître
            $table->unsignedBigInteger('fer_id');
            $table->timestamps();

            $table->foreign('col_id')->references('id')->on('collections')->onDelete('cascade');
            $table->foreign('fer_id')->references('id')->on('fermes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collecters');
    }
};
