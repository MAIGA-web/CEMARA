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
            $table->id();
            $table->boolean('per_etat')->default(true);

            $table->foreignId('poul_id')->constrained('poulaillers')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
                  
            $table->timestamps();
    $table->foreignId('fer_id')->constrained('fermes')->onDelete('cascade');

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
