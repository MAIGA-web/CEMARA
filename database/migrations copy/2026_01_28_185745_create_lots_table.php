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
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->string('origine');
            $table->integer('nbr')->check('nbr >=0');
            $table->boolean('lot_etat');

            $table->foreignId('poul_id')->constrained('poulaillers')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
                
            $table->foreignId('pro_id')->constrained('produits')
                ->onUpdate('cascade')
                ->onDelete('cascade');
    $table->foreignId('fer_id')->constrained('fermes')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};
