<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ferme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs selon les droits.
     */
    public function index()
    {
        $currentUser = auth()->user();

        if ($currentUser->user_etat == 1) {
            // Le Super Admin voit les utilisateurs de la ferme sélectionnée en session
            $users = User::where('fer_id', session('fer_id'))->get();
        } else {
            // L'Admin de ferme voit uniquement ceux de sa ferme
            $users = User::where('fer_id', $currentUser->fer_id)->get();
        }

        return view('Users.index', compact('users'));
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        return view('Users.add-edit', ['user' => new User()]);
    }

    /**
     * Enregistrement / Mise à jour.
     */
public function storeOrUpdate(Request $request, $id = null)
{
    // ÉTAPE 1 : Si on arrive en GET (clic sur lien), on affiche le formulaire
    if ($request->isMethod('get')) {
        $user = $id ? User::findOrFail($id) : new User();
        return view('Users.add-edit', compact('user'));
    }

    // ÉTAPE 2 : Si on arrive en POST (clic sur enregistrer), on valide et on sauvegarde
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email' . ($id ? ',' . $id : ''),
        'password' => $id ? 'nullable|min:6' : 'required|min:6',
        'user_etat' => 'required|integer',
    ]);

    $user = $id ? User::findOrFail($id) : new User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->user_etat = $request->user_etat;
    
    // On lie l'utilisateur à la ferme actuelle
    $user->fer_id = session('fer_id') ?? auth()->user()->fer_id;

    if ($request->filled('password')) {
        $user->password = bcrypt($request->password);
    }

    $user->save();

       return redirect()->route('Users.index')->with('success_message', 'Utilisateur enregistré.');
    }

    /**
     * Suppression d'un utilisateur.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Sécurité : ne pas se supprimer soi-même
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Impossible de supprimer votre propre compte.');
        }

        $user->delete();
        return redirect()->back()->with('success_message', 'Utilisateur supprimé.');
    }
}