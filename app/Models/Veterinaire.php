<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Veterinaire extends Model
{
        public $timestamps = false;
protected static function booted()
    {
        static::addGlobalScope('ferme', function ($builder) {
            if (session()->has('fer_id')) {
                $builder->where('fer_id', session('fer_id'));
            }
        });
    }
    //
}
