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
        Schema::create('acheters', function (Blueprint $table) {
            $table->id();
            $table->integer('act_qte')->check('act_qte >= 0');
            $table->decimal('act_pu',10,2)->check('act_pu >=0');
            $table->foreignId('pro_id')->constrained('produits')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('ac_id')->constrained('achats')
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
        Schema::dropIfExists('acheters');
    }
};
