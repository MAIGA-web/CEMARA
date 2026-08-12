<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ferme extends Model
{
    // Ajoute cette ligne pour autoriser l'enregistrement de ces colonnes
    protected $fillable = [
        'fer_nom',
        'fer_adresse',
        'fer_telephone',
        'fer_logo'
    ];

    // Relation vers les utilisateurs (Optionnel)
    public function users()
    {
        return $this->hasMany(User::class, 'fer_id');
    }
}