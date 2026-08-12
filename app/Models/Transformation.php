<?php

namespace App\Models;

use App\Models\Matiere;
use App\Models\Transformer;
use Illuminate\Database\Eloquent\Model;

class Transformation extends Model
{
    // protected $primaryKey = 'trans_id';
    protected $fillable = ['trans_qte', 'ma_id', 'fer_id', 'trans_etat'];

    public function matiere()
{
    return $this->belongsTo(Matiere::class, 'ma_id', 'id');
}

public function transformers()
{
    return $this->hasMany(Transformer::class, 'trans_id');
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
