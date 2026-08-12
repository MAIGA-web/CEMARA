<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transformer extends Model
{
    // Indique à Laravel le nom exact de votre table au pluriel inhabituel
    protected $table = 'transformers';
    public $timestamps = false;

    protected $fillable = ['trme_qte', 'trans_qte', 'pro_id', 'fer_id', 'trans_id'];

public function transformation()
{
    return $this->belongsTo(Transformation::class, 'trans_id');
}
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
