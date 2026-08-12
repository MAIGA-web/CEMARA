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
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->string('prodc_dure')->check('prodc_dure > 0');
            $table->integer('nbr_ouef')->check('nbr_ouef >= 0');
            $table->boolean('prodc_etat')->default(true);

            $table->foreignId('poul_id')->constrained('poulaillers')
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
        Schema::dropIfExists('productions');
    }
};
