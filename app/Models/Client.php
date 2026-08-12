<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use BelongsToFerme;
class Client extends Model

{
    public $timestamps = false;
    protected $fillable = ['cl_nom','cl_prenom','cl_adresse','cl_sexe','cl_tel','cl_etat', 'fer_id' ];

    protected static function booted()
{
    static::addGlobalScope('ferme', function ($builder) {
        if (session()->has('fer_id')) {
            $builder->where('fer_id', session('fer_id'));
        }
    });
}
}
