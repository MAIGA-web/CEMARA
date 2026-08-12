<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paiement extends Model
{
    protected $fillable = ['pa_payer', 'mod_id', 'vte_id', 'pa_etat', 'fer_id'];

    /**
     * Relation vers la Vente
     */
    public function vente(): BelongsTo
    {
        // On lie vte_id du paiement à l'id de la table ventes
        return $this->belongsTo(Vente::class, 'vte_id');
    }

    /**
     * Relation vers le Mode de paiement
     */
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