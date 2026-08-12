<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perte;
use App\Models\Perdre;
use App\Models\Produit;
use App\Models\Poulailler;
use Illuminate\Support\Facades\DB;

class PerteController extends Controller
{
    public function handleAction(Request $request)
    {
        $emp = $request->input('emp');
        $valider = $request->input('valider');
        $fer_id = $request->input('fer_id') ?? session('fer_id');
        $acc = $request->input('acc');

        // 1. INTERCEPTION POUR AFFICHER LE FORMULAIRE DE CRÉATION / MODIFICATION (En GET)
        if ($request->isMethod('get') && ($request->is('*add-edit*') || in_array($acc, ['M', 'C']))) {
            $id = $request->input('per_id');
            $perteEnEdition = $id ? Perte::find($id) : null;
            $poulaillers = Poulailler::where('fer_id', $fer_id)->where('poul_etat', 0)->orderBy('poul_nom')->get();
            
            return view('Pertes.create', compact('perteEnEdition', 'poulaillers', 'fer_id'));
        }

        // --- 2. TRAITEMENT DES ACTIONS (POST) ---
        if ($valider === 'Valider' || $valider === 'Oui') {
            switch ($emp) {
                case 'C': // Créer la fiche de Perte Maître
                    Perte::create([
                        'poul_id'  => $request->input('poul_id'),
                        'fer_id'   => $fer_id,
                        'per_etat' => 0, // En attente
                    ]);
                    session()->flash('success_message', 'Fiche de perte initiée avec succès !');
                    break;

                case 'U': // Modifier la fiche Maître
                    $perte = Perte::findOrFail($request->input('per_id'));
                    $perte->update([
                        'poul_id'    => $request->input('poul_id'),
                        'created_at' => $request->input('per_date'),
                    ]);
                    session()->flash('success_message', 'Fiche de perte mise à jour !');
                    break;

                case 'D': // Supprimer la fiche Maître (et ses composants par cascade)
                    Perte::destroy($request->input('per_id'));
                    session()->flash('success_message', 'Fiche de perte supprimée !');
                    break;

                // ------------------ SOUS-DÉTAILS : PERDRE ------------------
                case 'PC': // Ajouter un produit perdu (SANS toucher au stock)
                    $perd_qte = (int)$request->input('perd_qte');
                    $produit = Produit::findOrFail($request->input('pro_id'));

                    if ((int)$produit->pro_stock >= $perd_qte) {
                        Perdre::create([
                            'perd_qte' => $perd_qte,
                            'motif'    => $request->input('motif'),
                            'pro_id'   => $produit->id,
                            'per_id'   => $request->input('per_id'),
                            'fer_id'   => $fer_id
                        ]);
                        session()->flash('success_message', 'Ligne de perte ajoutée en attente !');
                    } else {
                        session()->flash('error_message', 'Attention : La quantité déclarée dépasse le stock disponible !');
                    }
                    break;

                case 'PU': // Modifier une ligne de perte (SANS toucher au stock)
                    $perdre = Perdre::findOrFail($request->input('perd_id'));
                    $perdre->update([
                        'perd_qte' => (int)$request->input('perd_qte'),
                        'motif'    => $request->input('motif'),
                        'pro_id'   => $request->input('pro_id')
                    ]);
                    session()->flash('success_message', 'Ligne de perte modifiée en attente !');
                    break;

                case 'PD': // Retirer un produit de la liste
                    Perdre::destroy($request->input('perd_id'));
                    session()->flash('success_message', 'Ligne de perte retirée !');
                    break;

                case 'PV': // VALIDATION GLOBALE : Déduction définitive des stocks !
                    $per_id = $request->input('per_id');
                    $perte = Perte::findOrFail($per_id);
                    $lignesPertes = Perdre::where('per_id', $per_id)->get();

                    if ($lignesPertes->isEmpty()) {
                        session()->flash('error_message', 'Impossible de valider une fiche de perte vide !');
                        break;
                    }

                    // Vérification globale des stocks avant action
                    foreach ($lignesPertes as $ligne) {
                        $produit = Produit::find($ligne->pro_id);
                        if (!$produit || (int)$produit->pro_stock < (int)$ligne->perd_qte) {
                            session()->flash('error_message', "Stock insuffisant pour valider la perte de : {$produit->pro_nom} !");
                            return redirect('/Pertes');
                        }
                    }

                    // Traitement de la déduction sécurisée
                    DB::transaction(function () use ($perte, $lignesPertes) {
                        $perte->update(['per_etat' => 1]); // Clôturée/Validée

                        foreach ($lignesPertes as $ligne) {
                            $produit = Produit::find($ligne->pro_id);
                            $produit->pro_stock = (int)$produit->pro_stock - (int)$ligne->perd_qte;
                            $produit->save();
                        }
                    });

                    session()->flash('success_message', 'Fiche de perte validée ! Le stock a été déduit.');
                    break;
            }
        }

        // --- CHARGEMENT DE L'INDEX ---
        $pertes = Perte::with(['poulailler'])
            ->where('fer_id', $fer_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $per_id = $request->input('per_id');
        $perteSelectionnee = $per_id ? Perte::with(['poulailler'])->find($per_id) : null;
        $detailsPerdre = $per_id ? Perdre::with(['produit'])->where('per_id', $per_id)->get() : collect();

        $perteEnEdition = in_array($acc, ['M', 'S', 'PV']) ? Perte::find($request->input('per_id')) : null;
        $perdreEnEdition = in_array($acc, ['AM', 'AS']) ? Perdre::with(['produit'])->find($request->input('perd_id')) : null;

        $produits = Produit::where('fer_id', $fer_id)->orderBy('pro_nom')->get();
        $poulaillers = Poulailler::where('fer_id', $fer_id)->where('poul_etat', 0)->orderBy('poul_nom')->get();

        return view('Pertes.index', compact(
            'pertes', 'perteSelectionnee', 'detailsPerdre', 
            'perteEnEdition', 'perdreEnEdition', 'produits', 'poulaillers', 'acc'
        ));
    }
}