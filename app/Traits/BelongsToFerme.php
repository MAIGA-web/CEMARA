<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToFerme {
    protected static function booted()
    {
        static::addGlobalScope('ferme', function (Builder $builder) {
            if (session()->has('fer_id')) {
                $builder->where('fer_id', session('fer_id'));
            }
        });

        // Ajout automatique du ferme_id lors de la création
        static::creating(function ($model) {
            if (session()->has('fer_id')) {
                $model->fer_id = session('fer_id');
            }
        });
        
    }
}