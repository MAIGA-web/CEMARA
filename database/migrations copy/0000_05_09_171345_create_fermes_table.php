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
        Schema::create('fermes', function (Blueprint $table) {
    $table->id();
    $table->string('fer_nom');
    $table->string('fer_adresse')->nullable();
    $table->string('fer_email')->nullable();
    $table->string('fer_telephone')->nullable();
    $table->string('fer_logo')->nullable(); // Pour le reçu personnalisé
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fermes');
    }
};
