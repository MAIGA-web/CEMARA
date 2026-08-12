<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use BelongsToFerme;
class Achat extends Model
{
    // public $timestamps = false;
    public function fournisseur() // Note le singulier ici
    {
        return $this->belongsTo(Fournisseur::class, 'four_id');
    }

    protected static function booted()
    {
        static::addGlobalScope('ferme', function ($builder) {
            if (session()->has('fer_id')) {
                $builder->where('fer_id', session('fer_id'));
            }
        });
    }
}
