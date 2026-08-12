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
        Schema::create('transformers', function (Blueprint $table) {
            $table->id();
            $table->integer('trme_qte')->check('trme_qte >=0');

            $table->foreignId('trans_qte')->constrained('transformations')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('pro_id')->constrained('produits')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
    $table->foreignId('fer_id')->constrained('fermes')->onDelete('cascade');

            // $table->id();
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transformers');
    }
};
