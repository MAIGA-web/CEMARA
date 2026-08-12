<?php

namespace App\Http\Controllers;

use App\Models\Achat;
use App\Models\Acheter;
use App\Models\Fournisseur;
use App\Models\Produit;
use App\Models\Mode;
use App\Models\Reglement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AchatController extends Controller
{
    // 1. Affichage principal (Liste + Détails si ID fourni)
    public function index(Request $request)
    {
        $fer_id = session('fer_id') ?? (auth()->user()->fer_id ?? null);
        $achats = Achat::with('fournisseur')->latest()->get();
        $achatSelectionnee = null;
        $produitAchete = [];
        $historique = [];
        $produits = Produit::all();
        $moes = Mode::where('fer_id', $fer_id)->get();
        if ($request->has('details')) {
            $achatSelectionnee = Achat::with('fournisseur')->find($request->details);
            $produitAchete = Acheter::with('produit')->where('ac_id', $achatSelectionnee->id)->get();
            $historique = Reglement::where('ac_id', $achatSelectionnee->id)->get();
        }
        return view('Achats.index', compact(
            'achats',
            'achatSelectionnee',
            'produitAchete',
            'historique',
            'produits',
            'moes'
        ));
    }
    // 2. Création ou modification d'un achat  (Fournisseurs / État)
    public function createOrUpdate(Request $request, $id = null)
    {
        $achat = $id ? Achat::findOrFail($id) : new Achat();
        if ($request->isMethod('post')) {
            $request->validate(
                [
                    'four_id' => 'required | exists:fournisseurs,id',
                ],
                [
                    'four_id.required' => 'Le choix de fournisseur est obligatoir',
                ]
            );
            $achat->four_id = $request->four_id;
            $achat->ac_etat = $request->has('ac_etat') ? true : false;
            $achat->fer_id = session('fer_id') ?? auth()->user()->fer_id;
            $achat->save();
            return redirect()->route('achat.index', ['detail' => $achat->id])
                ->with('sussess_message', 'Achat resu avec succès');
        }
        $fournisseur = Fournisseur::all();
        return view('Achats.create', compact('achat', 'fournisseur'));
    }

    // Ajouter de produits pour l'achat
    public function storeProduit(Request $request)
    {
        $request->validate([
            'ac_id' => 'required|exists:achats,id',
            'pro_id' => 'required|exists:produits,id',
            'act_qte' => 'required|numeric|min:0',
            'act_pu' => 'required|integer|min:1',
        ]);
        $stock = DB::table('produits')->where('id', $request->pro_id)
            ->selectRaw('SUM(pro_stock::numeric) as stock_actuel')
            ->value('stock_actuel') ?? 0;
        // if ($request->act_qte > $stock) {
        //     return redirect()->back()
        //         ->with('error_message', "Erreur : Le Stock saisi (" . number_format($request->act_qte, 0, ',', ' ') . ") 
        //             dépasse le reste du stock actuel (" . number_format($stock, 0, ',', ' ') . " ).")
        //         ->withInput();
        // }
        $acheter = new Acheter();
        $acheter->ac_id = $request->ac_id;
        $acheter->pro_id = $request->pro_id;
        $acheter->act_pu = $request->act_pu;
        $acheter->act_qte = $request->act_qte;
        $acheter->fer_id = session('fer_id') ?? auth()->user()->fer_id;
        $acheter->save();
        return redirect()->route('achat.index', [
            'details' => $request->ac_id,
            'tab' => 'pills-profile'
        ]);
    }
    // 4 Supprimer un produits d'un achats
    public function deleteProduit($id)
    {
        $acheter = Acheter::findOrFail($id);
        $ac_id = $acheter->ac_id;
        $acheter->delete();
        return redirect()->route('achat.index', ['details' => $ac_id, 'tab' => 'pills-profile'])
            ->with('success_message', 'Produit retiré de l\'achat.');
    }
    // 5. Supprimer un Achat entière
    public function delete($id)
    {
        $achat = Achat::findOrFail($id);
        // Supprime aussi les produits associés pour éviter les erreurs d'intégrité
        Acheter::where('ac_id', $achat->id)->delete();
        $achat->delete();

        return redirect()->route('achat.index')->with('success_message', 'Achat supprimée.');
    }
    // 6. Charger le formulaire de modification d'un produit (AJAX ou Page)
    public function editProduit($id)
    {
        $acheter = Acheter::findOrFail($id);
        $produits = Produit::all();
        $achatSelectionnee = Achat::find($acheter->ac_id);

        // On retourne une vue spécifique pour la modification
        return view('Achats.partials.edit_produit', compact('acheter', 'produits', 'achatSelectionnee'));
    }

    // 7. Enregistrer la modification
    public function updateProduit(Request $request, $id)
    {
        $request->validate([
            'act_pu' => 'required|numeric|min:0',
            'act_qte' => 'required|integer|min:1',
            'pro_id' => 'required|exists:produits,id',
        ]);

        $acheter = Acheter::findOrFail($id);
        $acheter->update([
            'pro_id' => $request->pro_id,
            'act_pu' => $request->act_pu,
            'act_qte' => $request->act_qte,
        ]);

        return redirect()->route('achat.index', ['details' => $acheter->ac_id, 'tab' => 'pills-profile'])
            ->with('success_message', 'Produit mis à jour avec succès.');
    }

    public function storeReglement(Request $request)
    {
        $request->validate([
            'ac_id' => 'required|exists:achats,id',
            're_mnt' => 'required|numeric|min:1',
            'mod_id' => 'required'
        ]);

        // 1. Calculer le montant total de la vente
        $totalAchat = DB::table('acheters')
            ->where('ac_id', $request->ac_id)
            ->selectRaw('SUM(act_pu * act_qte) as total')
            ->value('total') ?? 0;

        // 2. Calculer ce qui a déjà été payé
        $dejaPaye = Reglement::where('ac_id', $request->ac_id)->sum('re_mnt');

        $resteAPayer = $totalAchat - $dejaPaye;

        // 3. Vérification : le nouveau paiement ne doit pas dépasser le reste
        if ($request->re_mnt > $resteAPayer) {
            return redirect()->back()
                ->with('error_message', "Erreur : Le montant saisi (" . number_format($request->re_mnt, 0, ',', ' ') . " F) dépasse le reste à payer (" . number_format($resteAPayer, 0, ',', ' ') . " F).")
                ->withInput();
        }

        // 4. Si c'est bon, on enregistre
        $nombreReglements = Reglement::where('ac_id', $request->ac_id)->count();

        // 2. Préparer le libellé (Ex: Tranche 1, Tranche 2...)
        $nouvelleTranche = "Tranche " . ($nombreReglements + 1);
        $reglement = new Reglement();
        $reglement->ac_id = $request->ac_id;
        $reglement->re_mnt = $request->re_mnt;
        $reglement->mod_id = $request->mod_id;
        $reglement->mod_id = $request->mod_id;
        $reglement->re_etat = false;
        // $reglement->re_motif = $request->re_motif;
        $reglement->re_motif = $request->re_motif ?? $nouvelleTranche;
        $reglement->fer_id = session('fer_id') ?? auth()->user()->fer_id;
        $reglement->save();

        return redirect()->route('achat.index', ['details' => $request->ac_id, 'tab' => 'pills-paie'])
            ->with('success_message', 'Reglement enregistré.');
    }

    // Modifier un paiement (Formulaire)
    public function editReglement($id)
    {
        $reglement = Reglement::findOrFail($id);
        $achat = Achat::findOrFail($reglement->ac_id);

        // Sécurité : si la vente est validée, interdiction de modifier
        if ($reglement->re_etat) {
            return redirect()->back()->with('error_message', 'Impossible de modifier un reglement sur un achat validée.');
        }

        $fer_id = session('fer_id') ?? auth()->user()->fer_id;
        $modes = Mode::where('fer_id', $fer_id)->get();
        return view('Achats.partials.edit_reglement', compact('reglement', 'modes', 'achat'));
    }

    // Update du reglement
public function updateReglement(Request $request, $id)
{
    $reglement = Reglement::findOrFail($id);

    $request->validate([
        're_mnt' => 'required|numeric|min:1',
        'mod_id' => 'required'
    ]);
    
    $ac_id = $reglement->ac_id;
    $totalAchat = DB::table('acheters')
        ->where('ac_id', $ac_id)
        ->selectRaw('SUM(act_pu * act_qte) as total')
        ->value('total') ?? 0;

    // 2. Calcul de ce qui est payé par les AUTRES (on exclut ce paiement-ci)
    $reglerParAutres = \App\Models\Reglement::where('ac_id', $ac_id)
        ->where('id', '!=', $id) // CRITIQUE : Ne pas se compter soi-même
        ->sum('re_mnt');

    $maxAutorise = $totalAchat - $reglerParAutres;

    if ($request->re_mnt > $maxAutorise) {
        return redirect()->route('reglement.edit', $id)
            ->with('error_messages', 'Le montant dépasse le total de l\'achat (' . number_format($maxAutorise, 0, ',', ' ') . ' F max).')
            ->withInput(); // Garde le montant saisi dans le formulaire pour éviter de tout retaper
    }

    $reglement->update([
        're_mnt' => $request->re_mnt,
        'mod_id' => $request->mod_id,
    ]);

    return redirect()->route('achat.index', ['details' => $reglement->ac_id, 'tab' => 'pills-paie'])
        ->with('success_message', 'Reglement mis à jour.');
}
    // Valider Reglement
    public function validerReglement($id)
    {
        $reglement = Reglement::findOrFail($id);
        $reglement->re_etat = true; // On valide le Reglement
        $reglement->save();

        return redirect()->back()->with('success_message', 'Le Reglement a été validé et verrouillé.');
    }

    // Supprimer un Reglement
    public function deleteReglement($id)
    {
        $reglement = Reglement::findOrFail($id);
        $achat = Achat::find($reglement->ac_id);

        if ($reglement->re_etat) {
            return redirect()->back()->with('error_message', 'Action impossible : achat déjà validée.');
        }

        $reglement->delete();
        return redirect()->back()->with('success_message', 'Reglement supprimé.');
    }

    public function valider($id)
    {
        // On récupère l'achat avec sa ferme
        $achat = Achat::findOrFail($id);

        // 1. Récupérer tous les produits liés à cet achat
        $produitsAcheters = Acheter::where('ac_id', $id)->get();

        // 2. Mettre à jour le stock
        foreach ($produitsAcheters as $item) {
            // le produit qui a cet ID ET qui appartient à la ferme de l'achat
            $produit = Produit::where('id', $item->pro_id)
                ->where('fer_id', $achat->fer_id) // Filtre par ferme
                ->first();

            if ($produit) {
                $produit->pro_stock += $item->act_qte;
                $produit->save();
            } else {
                // Optionnel : Gérer le cas où le produit n'existe pas dans cette ferme
                return back()->with('error', "Le produit {$item->pro_id} n'appartient pas à cette ferme.");
            }
        }

        // 3. Valider l'achat
        $achat->ac_etat = true;
        $achat->save();

        return redirect()->route('achat.index', ['details' => $id])
            ->with('success_message', 'Achat validé et stock de la ferme mis à jour !');
    }
}
