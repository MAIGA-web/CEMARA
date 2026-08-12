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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->decimal('pa_payer',10,2)->check('pa_payer >=0');
            $table->boolean('pa_etat')->default(true);
            $table->foreignId('vte_id')->constrained('ventes')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->foreignId('mod_id')->constrained('modes')
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
        Schema::dropIfExists('paiements');
    }
};
