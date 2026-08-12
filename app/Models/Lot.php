<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; 
use Carbon\Carbon;

class Lot extends Model
{
    // Indique à Laravel de traiter ces colonnes comme de vraies dates Carbon
    protected $fillable = [
        'fer_id', 
        'poul_id', 
        'lot_code', 
        'pro_id', 
        'lot_qte_initiale', 
        'lot_date_arrivee', 
        'lot_date_sortie_prevue', 
        'lot_actif',
        'origine'
    ];

    protected $casts = [
        'lot_date_arrivee' => 'date',
        'lot_date_sortie_prevue' => 'date',
        'lot_actif' => 'boolean'
    ];

    // Relation vers les suivis de ce lot
    public function suivis()
    {
        return $this->hasMany(SuiviJournalier::class, 'lot_id');
    }

    /**
     * Calcul dynamique de l'âge du lot (Nombre de jours depuis l'arrivée)
     * Accessible via : $lot->age_poussins
     */
    public function getAgePoussinsAttribute()
    {
        if (!$this->lot_date_arrivee) return 0;
        
        if ($this->lot_actif) {
            return (int) $this->lot_date_arrivee->diffInDays(Carbon::now());
        }
        return (int) $this->lot_date_arrivee->diffInDays($this->updated_at);
    }

    /**
     * Somme des pertes cumulées du lot
     * Accessible via : $lot->total_morts
     */
    public function getTotalMortsAttribute()
    {
        return (int) $this->suivis()->sum('morts_jour');
    }

    /**
     * Nombre d'oiseaux encore en vie
     * Accessible via : $lot->reste_vivants
     */
    public function getResteVivantsAttribute()
    {
        return (int) ($this->lot_qte_initiale - $this->total_morts);
    }
    public function poulailler(): BelongsTo
    {
        // Remplace 'poul_id' par le vrai nom de ta colonne si nécessaire (ex: 'poulailler_id')
        return $this->belongsTo(Poulailler::class, 'poul_id'); 
    }


    /**
     * 🟢 Relation avec le Produit (Type d'arrivage)
     */
    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'pro_id');
    }

}