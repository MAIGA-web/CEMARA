<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuiviJournalier extends Model
{
    protected $table = 'suivi_journaliers';

    protected $fillable = [
        'fer_id', 
        'lot_id', 
        'suivi_date', 
        'morts_jour', 
        'consommation_aliment', 
        'etat_sante', 
        'observations'
    ];

    protected $casts = [
        'suivi_date' => 'date',
        'consommation_aliment' => 'float',
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class, 'lot_id');
    }
}