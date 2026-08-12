<?php

namespace App\Models;
use App\Models\Client;

use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    protected $fillable = ['vte_etat','cl_id','fer_id'];
    public function client()
    {
        return $this->belongsTo(Client::class, 'cl_id');
    }
    public function ferme()
    {
        return $this->belongsTo(Ferme::class, 'fer_id');
    }
        protected static function booted()
{
    static::addGlobalScope('ferme', function ($builder) {
        if (session()->has('fer_id')) {
            $builder->where('fer_id', session('fer_id'));
        }
    });
}
public function paiements()
{
    // Relation : Une vente possède plusieurs paiements
    return $this->hasMany(Paiement::class, 'vte_id');
}
}
