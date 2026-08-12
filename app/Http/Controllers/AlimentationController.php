<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alimentation;
use App\Models\Alimenter;
use App\Models\Produit;
use App\Models\Poulailler;
use Illuminate\Support\Facades\DB;

class AlimentationController extends Controller
{
    public function handleAction(Request $request)
    {
        $emp = $request->input('emp');
        $valider = $request->input('valider');
        $fer_id = $request->input('fer_id') ?? session('fer_id');
        $acc = $request->input('acc');

        // 1. INTERCEPTION POUR AFFICHER LE FORMULAIRE DE CRÉATION / MODIFICATION (En GET)
        // On intercepte si l'URL contient "add-edit" OU si l'action 'acc' vaut 'M' ou 'C'
        if ($request->isMethod('get') && ($request->is('*add-edit*') || in_array($acc, ['M', 'C']))) {

            $id = $request->input('alm_id');
            // Si un ID est fourni, on cherche la fiche à modifier, sinon null pour une création
            $alimentationEnEdition = $id ? Alimentation::find($id) : null;

            // Chargement de la liste des poulaillers pour le select
            $poulaillers = Poulailler::where('fer_id', $fer_id)->where('poul_etat', 0)->orderBy('poul_nom')->get();

            // On force l'affichage immédiat de la vue de création
            return view('Alimentations.create', compact('alimentationEnEdition', 'poulaillers', 'fer_id'));
        }

        // --- 2. TRAITEMENT DES ACTIONS (POST) ---
        if ($valider === 'Valider' || $valider === 'Oui') {
            switch ($emp) {
                case 'C': // Créer Alimentation (Maitre)
                    Alimentation::create([
                        'poul_id'    => $request->input('poul_id'),
                        'fer_id'     => $fer_id,
                        'alm_etat'   => 0, // En attente
                    ]);
                    session()->flash('success_message', 'Alimentation créée avec succès !');
                    break;

                case 'U': // Modifier Alimentation
                    $id = $request->input('alm_id');
                    $alimentation = Alimentation::findOrFail($id);
                    $alimentation->update([
                        'poul_id'    => $request->input('poul_id'),
                        'created_at' => $request->input('alm_date'),
                    ]);
                    session()->flash('success_message', 'Alimentation mise à jour !');
                    break;

                case 'D': // Supprimer Alimentation
                    Alimentation::destroy($request->input('alm_id'));
                    session()->flash('success_message', 'Alimentation supprimée !');
                    break;

                // ------------------ SOUS-DÉTAILS : ALIMENTER ------------------
                case 'AC': // Ajouter un aliment (SANS déduire le stock maintenant)
                    $pro_id = $request->input('pro_id');
                    $almt_qte = (int)$request->input('almt_qte');
                    $produit = Produit::where('pro_etat', 2)->findOrFail($pro_id);

                    // Vérification simple par sécurité vis-à-vis du stock théorique
                    if ((int)$produit->pro_stock >= $almt_qte) {
                        Alimenter::create([
                            'almt_qte' => $almt_qte,
                            'pro_id'   => $produit->id,
                            'alm_id'   => $request->input('alm_id'),
                            'fer_id'   => $fer_id
                        ]);
                        session()->flash('success_message', 'Aliment ajouté à la liste en attente !');
                    } else {
                        session()->flash('error_message', 'Attention : La quantité demandée dépasse le stock disponible !');
                    }
                    break;

                case 'AU': // Modifier un aliment distribué (SANS toucher au stock)
                    $almt_id = $request->input('almt_id');
                    $almt_qte_nouvelle = (int)$request->input('almt_qte');

                    $alimenter = Alimenter::findOrFail($almt_id);
                    $alimenter->update([
                        'almt_qte' => $almt_qte_nouvelle,
                        'pro_id'   => $request->input('pro_id')
                    ]);

                    session()->flash('success_message', 'Quantité mise à jour en attente !');
                    break;

                case 'AD': // Retirer un aliment de la liste (SANS toucher au stock)
                    Alimenter::destroy($request->input('almt_id'));
                    session()->flash('success_message', 'Ligne d\'aliment retirée !');
                    break;

                case 'AV': // VALIDATION GLOBALE : C'est ici qu'on déduit tout le stock !
                    $alm_id = $request->input('alm_id');
                    $alimentation = Alimentation::findOrFail($alm_id);

                    // 1. Récupérer tous les aliments rattachés à cette fiche
                    $lignesAliments = Alimenter::where('alm_id', $alm_id)->get();

                    if ($lignesAliments->isEmpty()) {
                        session()->flash('error_message', 'Impossible de valider une fiche vide sans aucun aliment !');
                        break;
                    }

                    // 2. Vérifier si le stock de CHAQUE produit est suffisant avant de faire quoi que ce soit
                    foreach ($lignesAliments as $ligne) {
                        $produit = Produit::find($ligne->pro_id);
                        if (!$produit || (int)$produit->pro_stock < (int)$ligne->almt_qte) {
                            session()->flash('error_message', "Stock insuffisant pour le produit : {$produit->pro_nom} !");
                            return redirect('/Alimentations');
                        }
                    }

                    // 3. Si tout est OK, on passe à la déduction générale sécurisée par transaction
                    DB::transaction(function () use ($alimentation, $lignesAliments) {
                        // Passer la fiche à l'état validé (1)
                        $alimentation->update(['alm_etat' => 1]);

                        // Boucle de déduction définitive pour chaque produit
                        foreach ($lignesAliments as $ligne) {
                            $produit = Produit::find($ligne->pro_id);

                            // Calcul en PHP pour éviter les conflits de types varchar/int PostgreSQL
                            $produit->pro_stock = (int)$produit->pro_stock - (int)$ligne->almt_qte;
                            $produit->save();
                        }
                    });

                    session()->flash('success_message', 'Fiche validée avec succès ! Le stock des produits a été déduit.');
                    break;
            }
        }

        // --- CHARGEMENT DES DONNÉES DE L'INDEX ---
        $alimentations = Alimentation::with(['poulailler'])
            ->where('fer_id', $fer_id)
            ->orderBy('created_at', 'desc')
            ->get();

        // $alm_id = $request->input('alm_id') ?? ($alimentations->first()->id ?? null);
        $alm_id = $request->input('alm_id');
        $alimentationSelectionnee = $alm_id ? Alimentation::with(['poulailler'])->find($alm_id) : null;

        $detailsAlimenter = $alimentationSelectionnee ? Alimenter::with(['produit'])->where('alm_id', $alm_id)->get() : collect();

        $acc = $request->input('acc');
        $alimentationEnEdition = in_array($acc, ['M', 'S', 'AV']) ? Alimentation::find($request->input('alm_id')) : null;
        $alimenterEnEdition = in_array($acc, ['AM', 'AS']) ? Alimenter::with(['produit'])->find($request->input('almt_id')) : null;

        $produits = Produit::where('pro_etat', 2)->where('fer_id', $fer_id)->orderBy('pro_nom')->get();
        $poulaillers = Poulailler::where('fer_id', $fer_id)->where('poul_etat', 0)->orderBy('poul_nom')->get();

        return view('Alimentations.index', compact(
            'alimentations',
            'alimentationSelectionnee',
            'detailsAlimenter',
            'alimentationEnEdition',
            'alimenterEnEdition',
            'produits',
            'poulaillers',
            'acc'
        ));
    }
}
