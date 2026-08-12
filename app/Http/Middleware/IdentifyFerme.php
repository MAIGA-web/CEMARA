<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;


class IdentifyFerme
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si l'utilisateur est connecté et que la session n'a pas encore le fer_id
        if (Auth::check() && !session()->has('fer_id')) {
            $user = Auth::user();
            
            // On stocke l'ID de la ferme en session
            session(['fer_id' => $user->fer_id]);
            
$ferme = DB::table('fermes')->find($user->fer_id);
// Utilise l'opérateur de coalescence sécurisée
session(['fer_nom' => $ferme->fer_nom ?? 'Ma Ferme']);
        }

        return $next($request);
    }
}