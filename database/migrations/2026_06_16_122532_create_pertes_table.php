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
        Schema::create('pertes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('per_etat')->default(true);
            $table->bigInteger('poul_id');
            $table->timestamps();
            $table->bigInteger('fer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertes');
    }
};
