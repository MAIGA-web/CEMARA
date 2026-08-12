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
        Schema::create('produires', function (Blueprint $table) {
            $table->id();
            $table->integer('prdr_qte')->check('prdr_qte >=0');

            $table->foreignId('prodc_id')->constrained('productions')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->foreignId('pro_id')->constrained('produits')
                ->onUpdate('cascade')
                ->onDelete('cascade');
    $table->foreignId('fer_id')->constrained('fermes')->onDelete('cascade');

            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produires');
    }
};
