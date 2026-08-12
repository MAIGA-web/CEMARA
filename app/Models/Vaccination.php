<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vaccination extends Model
{
    protected $fillable = ['vac_qte', 'pro_id', 'vtr_id', 'poul_id', 'fer_id', 'vac_etat', 'vac_date'];
    
// public $timestamps = false;

    public function produit() { return $this->belongsTo(Produit::class, 'pro_id'); }
    public function poulailler() { return $this->belongsTo(Poulailler::class, 'poul_id'); }
    public function veterinaire() { return $this->belongsTo(Veterinaire::class, 'vtr_id'); }
    public function ferme() { return $this->belongsTo(Ferme::class, 'fer_id'); }
}