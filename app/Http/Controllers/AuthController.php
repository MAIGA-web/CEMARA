<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Ferme;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    // --- CONNEXION ---

    public function showLogin() {
        return view('connexion');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // 1. REDIRECTION IMMÉDIATE POUR SUPER ADMIN (user_etat == 1)
            if ($user->user_etat == 1) {
                return redirect()->route('SuperAdmin.index');
            }

            // 2. RÉCUPÉRATION SÉCURISÉE DE LA FERME
            $ferme = $user->fer_id ? Ferme::find($user->fer_id) : null;
            
            // Si la ferme existe et est suspendue (ex: fer_etat == 1)
            if ($ferme && isset($ferme->fer_etat) && $ferme->fer_etat == 1) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return back()->withErrors(['auth' => 'Votre ferme est suspendue. Accès refusé.']);
            }

            // 3. STOCKAGE EN SESSION AVEC VALEUR PAR DÉFAUT (Évite le crash sur null)
            session([
                'fer_id'  => $user->fer_id,
                'fer_nom' => $ferme ? $ferme->fer_nom : 'Ma Ferme'
            ]);

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['auth' => 'Email ou mot de passe incorrect.']);
    }

    // --- INSCRIPTION (CRÉATION DE FERME) ---

    public function showRegister() {
        return view('inscription');
    }

    public function register(Request $request): RedirectResponse {
        $request->validate([
            'nom_ferme' => 'required|string|max:255',
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|confirmed|min:6',
            'user_etat' => 'required',
        ]);

        // 1. Créer la Ferme d'abord
        $ferme = Ferme::create([
            'fer_nom' => $request->nom_ferme,
        ]);

        // 2. Créer l'Utilisateur lié à cette ferme
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'fer_id'    => $ferme->id,
            'role'      => 'proprietaire',
            'user_etat' => $request->user_etat,
        ]);

        // 3. Connecter l'utilisateur immédiatement
        Auth::login($user);
        
        // 4. Redirection selon l'état du compte créé
        if ($user->user_etat == 1) {
            return redirect()->route('SuperAdmin.index');
        }

        session([
            'fer_id'  => $user->fer_id,
            'fer_nom' => $ferme->fer_nom
        ]);

        return redirect()->route('dashboard');
    }

    // --- DÉCONNEXION ---

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}