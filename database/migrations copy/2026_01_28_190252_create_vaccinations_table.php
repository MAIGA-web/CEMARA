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
        Schema::create('vaccinations', function (Blueprint $table) {
            $table->id();
            $table->integer('vac_qte')->check('vac_qte');
             $table->foreignId('pro_id')->constrained('produits')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->foreignId('vtr_id')->constrained('veterinaires')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreignId('poul_id')->constrained('poulaillers')
            ->onUpdate('cascade')
            ->onDelete('cascade');
    $table->foreignId('fer_id')->constrained('fermes')->onDelete('cascade');

            $table->boolean('vac_etat')->defaut(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccinations');
    }
};
