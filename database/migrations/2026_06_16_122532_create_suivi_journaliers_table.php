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
        Schema::create('suivi_journaliers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fer_id');
            $table->bigInteger('lot_id');
            $table->date('suivi_date');
            $table->integer('morts_jour')->default(0);
            $table->decimal('consommation_aliment')->default(0);
            $table->string('etat_sante')->default('Sain');
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suivi_journaliers');
    }
};
