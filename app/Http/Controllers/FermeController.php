<?php

namespace App\Http\Controllers;

use App\Models\Ferme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FermeController extends Controller
{
    // Liste des fermes + Affichage de la Fiche Profil de la ferme sélectionnée
    public function index(Request $request)
    {
        $fermes = Ferme::all();

        // 1. On cherche l'ID de la ferme : soit passé dans l'URL (?fer_id=X), soit en session
        $fer_id = $request->input('fer_id') ?? session('fer_id');

        // 2. Si aucun ID n'est trouvé, on sélectionne la 1ère ferme de la base par défaut
        if (!$fer_id && $fermes->isNotEmpty()) {
            $fer_id = $fermes->first()->id;
        }

        // 3. Récupération de la ferme sélectionnée
        $ferme_selectionnee = $fer_id ? Ferme::find($fer_id) : null;

        // 4. Si la ferme existe, on enregistre/rafraîchit la session
        if ($ferme_selectionnee) {
            session([
                'fer_id' => $ferme_selectionnee->id,
                'fer_nom' => $ferme_selectionnee->fer_nom
            ]);
        }

        // 5. /!\ TRÈS IMPORTANT : On passe bien 'ferme_selectionnee' à la vue
        return view('SuperAdmin.index', compact('fermes', 'ferme_selectionnee'));
    }

    // Affiche le profil de la ferme actuelle (Configuration)
    public function monProfil()
    {
        $id = session('fer_id');
        $ferme = Ferme::findOrFail($id);
        return view('Fermes.add-edit', compact('ferme'));
    }

    // Ajout ou Modification
    public function storeOrUpdate(Request $request, $id = null)
    {
        // --- PARTIE 1 : AFFICHAGE DU FORMULAIRE (GET) ---
        if ($request->isMethod('get')) {
            $ferme = $id ? Ferme::findOrFail($id) : new Ferme();
            return view('Fermes.add-edit', compact('ferme'));
        }

        // --- PARTIE 2 : ENREGISTREMENT (POST) ---
        $request->validate([
            'fer_nom' => 'required|string|max:255',
        ]);

        $ferme = $id ? Ferme::findOrFail($id) : new Ferme();
        
        $ferme->fer_nom = $request->fer_nom;
        $ferme->fer_adresse = $request->fer_adresse;
        $ferme->fer_telephone = $request->fer_telephone;
        $ferme->fer_etat = $request->fer_etat;
        $ferme->fer_email = $request->fer_email;

        if ($request->hasFile('fer_logo')) {
            if($ferme->fer_logo){
                Storage::disk('public')->delete($ferme->fer_logo);
            }
            
            $path = $request->file('fer_logo')->store('logos', 'public');
            $ferme->fer_logo = $path;
        }

        $ferme->save();

        return redirect('/Fermes')->with('success_message', $id ? 'Ferme modifiée !' : 'Ferme créée avec succès !');
    }

    // Changement d'état (Activer/Désactiver une ferme)
    public function toggleEtat($id)
    {
        $ferme = Ferme::findOrFail($id);
        $ferme->fer_etat = !$ferme->fer_etat;
        $ferme->save();

        return redirect()->back()->with('status', 'État de la ferme modifié.');
    }

    // Suppression
    public function destroy($id)
    {
        $ferme = Ferme::findOrFail($id);
        
        if ($id == session('fer_id')) {
            return redirect()->back()->with('error', 'Action impossible sur la ferme active.');
        }

        $ferme->delete();
        return redirect()->back()->with('success', 'Ferme supprimée avec succès.');
    }

    // Sélection d'une ferme depuis le formulaire/menu
    public function choisir(Request $request)
    {
        $request->validate([
            'fer_id' => 'required|exists:fermes,id'
        ]);

        $ferme = Ferme::findOrFail($request->fer_id);

        session([
            'fer_id' => $ferme->id,
            'fer_nom' => $ferme->fer_nom
        ]);

        // Redirige vers la page des fermes avec le paramètre de la ferme sélectionnée
        return redirect()->route('SuperAdmin.index', ['fer_id' => $ferme->id]);
    }
}