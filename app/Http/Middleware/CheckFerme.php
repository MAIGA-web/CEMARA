<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFerme
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle(Request $request, Closure $next)
{
    if (auth()->check() && !session()->has('fer_id')) {
        // On prend la première ferme de l'utilisateur par défaut
        session(['fer_id' => auth()->user()->fer_id]);
    }
    return $next($request);
}
}
