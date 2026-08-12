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
        Schema::create('transformations', function (Blueprint $table) {
            $table->id();
            $table->integer('trans_qte')->check('trans_qte >=0');

            $table->foreignId('ma_id')->constrained('matieres')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
    $table->foreignId('fer_id')->constrained('fermes')->onDelete('cascade');

            $table->boolean('trans_etat')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transformations');
    }
};
