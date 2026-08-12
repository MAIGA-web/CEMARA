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
        Schema::create('poulaillers', function (Blueprint $table) {
            $table->id();
            $table->string('poul_nom')->unique();
            $table->integer('poul_capa')->check('poul_capa >=0');
            $table->string('poul_empl');
            $table->boolean('poul_etat')->default(true);
    $table->foreignId('fer_id')->constrained('fermes')->onDelete('cascade');

            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poulaillers');
    }
};
