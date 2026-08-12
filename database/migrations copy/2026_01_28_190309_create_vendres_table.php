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
        Schema::create('vendres', function (Blueprint $table) {
            $table->id();
            $table->decimal('vdr_pu',10,2)->check('vdr_pu >=0');
            $table->integer('vdr_qte')->check('vdr_qte >=0');

            $table->foreignId('vte_id')->constrained('ventes')
            ->onUpdate('cascade')
            ->onDelete('cascade');
    $table->foreignId('fer_id')->constrained('fermes')->onDelete('cascade');

            $table->foreignId('pro_id')->constrained('produits')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendres');
    }
};
