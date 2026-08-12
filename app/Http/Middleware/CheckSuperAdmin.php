<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Si l'utilisateur n'est même pas connecté, retour direct au login
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
        }

        // 2. Vérification stricte du rôle Super Admin (user_etat == 1)
        if (auth()->user()->user_etat == 1) {
            return $next($request);
        }

        // 3. Si c'est un utilisateur normal (ex: proprietaire de ferme), redirection vers son dashboard
        return redirect()->route('dashboard')->with('error', 'Accès réservé au Super Admin.');
    }
}