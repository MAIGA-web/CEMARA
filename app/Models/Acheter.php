<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use BelongsToFerme;

class Acheter extends Model
{

        public $timestamps = false;
      protected $fillable = ['act_pu','ac_id','pro_id','act_qte'];
        public function produit()
    {
        return $this->belongsTo(Produit::class, 'pro_id');
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
