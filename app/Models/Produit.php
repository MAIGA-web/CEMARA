<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToFerme; // Importe le trait

class Produit extends Model
{
    protected $primaryKey = 'id';
        public $timestamps = false;
    use BelongsToFerme; // Active le filtrage automatique par fer_id
    
    protected $fillable = ['pro_nom', 'pro_type', 'pro_stock','pro_etat','fer_id'];
}
