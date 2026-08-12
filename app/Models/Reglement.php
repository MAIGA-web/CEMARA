<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Reglement extends Model
{
     protected $fillable = ['re_mnt', 'mod_id', 'ac_id', 're_etat', 'fer_id'];

      public function mode(): BelongsTo
    {
        // On lie mod_id du paiement à l'id de la table modes
        return $this->belongsTo(Mode::class, 'mod_id');
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
