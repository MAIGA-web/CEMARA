<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collecter extends Model
{
    // On indique à Laravel le nom exact de la table au pluriel (Convention)
    protected $table = 'collecters';

    // IMPORTANT : On autorise explicitement l'écriture dans nos 3 nouvelles colonnes
    protected $fillable = [
        'qte_ramasse',
        'qte_casse',
        'qte_consomme',
        'col_id',
        'fer_id'
    ];

    // Liaison inverse vers la fiche Maître
    public function collection()
    {
        return $this->belongsTo(Collection::class, 'col_id');
    }
}