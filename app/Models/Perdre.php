<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perdre extends Model
{
     public $timestamps = false; // Pas de created_at/updated_at dans ta structure SQL

     protected $fillable = ['perd_qte', 'per_id', 'pro_id', 'fer_id','motif'];

    public function produit() {
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
