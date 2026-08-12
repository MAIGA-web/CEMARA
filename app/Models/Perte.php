<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perte extends Model
{
     protected $fillable = [
        'per_etat', 
        'poul_id', 
        'fer_id'
    ];

    /**
     * Relation avec le Poulailler
     */
    public function poulailler()
    {
        // On lie poul_id (de la table productions) à l'id (de la table poulaillers)
        return $this->belongsTo(Poulailler::class, 'poul_id');
    }}
