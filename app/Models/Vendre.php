<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendre extends Model
{
    public $timestamps = false;
      protected $fillable = ['vdr_pu','vte_id','pro_id','vdr_qte'];
    public function produit()
    {
        return $this->belongsTo(Produit::class, 'pro_id');
    }

     public function vente()
    {
        return $this->belongsTo(Vente::class, 'vte_id');
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
