<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Mode extends Model
{
    protected $table = 'modes';

    protected static function booted()
    {
        static::addGlobalScope('ferme', function (Builder $builder) {
            // Si on consulte une vente spécifique dans la requête
            $vte_id = request('details') ?? request('vte_id');
            
            if ($vte_id) {
                $vente = Vente::find($vte_id);
                if ($vente && $vente->fer_id) {
                    $builder->where('fer_id', $vente->fer_id);
                    return;
                }
            }

            // Sinon on applique le filtre de la session/utilisateur
            $fer_id = session('fer_id') ?? (auth()->user()->fer_id ?? null);
            if ($fer_id) {
                $builder->where('fer_id', $fer_id);
            }
        });
    }
}