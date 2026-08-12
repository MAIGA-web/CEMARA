<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    // protected $primaryKey = 'ma_id';
    public $timestamps = false;
    protected static function booted()
    {
        static::addGlobalScope('ferme', function ($builder) {
            if (session()->has('fer_id')) {
                $builder->where('fer_id', session('fer_id'));
            }
        });
    }
    protected $fillable = ['ma_nom', 'ma_type', 'ma_stock', 'ma_etat', 'fer_id'];

    public function transformations()
    {
        return $this->hasMany(Transformation::class, 'ma_id');
    }
}
