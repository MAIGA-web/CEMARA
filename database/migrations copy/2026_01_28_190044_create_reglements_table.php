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
        Schema::create('reglements', function (Blueprint $table) {
            $table->id();
            $table->decimal('re_mnt',10,2)->check('re_mnt >=0');
            $table->string('re_motif');

            $table->foreignId('ac_id')->constrained('achats')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            
            $table->foreignId('mod_id')->constrained('modes')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->boolean('re_etat')->default(true);
    $table->foreignId('fer_id')->constrained('fermes')->onDelete('cascade');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reglements');
    }
};
