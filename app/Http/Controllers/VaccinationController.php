<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vaccination;
use App\Models\Produit;
use App\Models\Veterinaire;
use App\Models\Poulailler;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VaccinationController extends Controller
{
    public function handleAction(Request $request)
    {

if ($request->isMethod('get') && ($request->is('Vaccinations/add-edit*') || $request->input('form') == 'M')) {
        
        $fer_id = $request->input('fer_id') ?? session('fer_id');
        $id = $request->route('id') ?? $request->input('vac_id');
        
        // Si ID présent -> Modification, sinon -> Ajout
        $vaccination = $id ? Vaccination::find($id) : new Vaccination();
        
        // Listes de chargement pour les selects
        $produits = Produit::where('pro_etat', 0)->where('fer_id', $fer_id)->orderBy('pro_nom')->get();
        $veterinaires = Veterinaire::where('fer_id', $fer_id)->where('vtr_etat', 't')->orderBy('vtr_nom')->get();
        $poulaillers = Poulailler::where('fer_id', $fer_id)->where('poul_etat', 0)->orderBy('poul_nom')->get();
        
        return view('Vaccinations.create', compact('vaccination', 'produits', 'veterinaires', 'poulaillers', 'fer_id'));
    }

    // 2. TRAITEMENT DES ACTIONS (S'exécute si c'est un POST, DELETE ou l'index classique)
    $emp = $request->input('emp');
    $valider = $request->input('valider');
    
    $fer_id = $request->input('fer_id') ?? session('fer_id');
    $pro_id = $request->input('pro_id');

        // --- GESTION DES ACTIONS (SWITCH) ---
        if ($valider === 'Valider' || $valider === 'Oui') {
            switch ($emp) {
                case 'C': // Création
                    // pro_etat = 0 car d'après votre vue Produit, 0 = Non vendre (Vaccin)
                    $produit = Produit::where('pro_etat', 0)->find($pro_id);
                    $vac_qte = $request->input('vac_qte');

                    if ($produit && $produit->pro_stock >= $vac_qte) {
                        DB::transaction(function () use ($request, $produit, $vac_qte, $fer_id) {
                            Vaccination::create([
                                'vac_qte'  => $vac_qte,
                                'pro_id'   => $request->input('pro_id'),
                                'vtr_id'   => $request->input('vtr_id'),
                                'poul_id'  => $request->input('poul_id'),
                                'fer_id'   => $fer_id,
                                // 'created_at' => $request->input('created_at'),
                                'vac_etat' => 0 // Par défaut en attente
                            ]);
                            // Note : Votre logique d'origine retire le stock dès la création. 
                            // Si vous préférez le retirer uniquement à la validation (Case V), supprimez la ligne suivante.
                       
                            // $produit->decrement('pro_stock', $vac_qte);
                    });
                        session()->flash('success_message', 'Succès - Opération reçue !');
                    } else {
                        session()->flash('error_message', 'Échec - Impossible de valider car la quantité dépasse le stock actuel !');
                    }
                    break;

                case 'U': // Modification
                    $id_v = $request->input('vac_id');
                    $vac_qte = $request->input('vac_qte');

                    $produit = Produit::where('pro_etat', 0)->find($pro_id);
                    $vaccination = Vaccination::find($id_v);

                    if ($produit && $vaccination) {
                        // Logique d'origine : Vérification si (Nouvelle Qte) > Stock actuel
                        if ($vac_qte > $produit->pro_stock) {
                            session()->flash('error_message', 'Échec - Impossible de valider car la quantité dépasse le stock actuel !');
                        } else {
                            $vaccination->update([
                                'created_at' => $request->input('created_at'),
                                'pro_id'   => $pro_id,
                                'vtr_id'   => $request->input('vtr_id'),
                                'poul_id'  => $request->input('poul_id'),
                                'fer_id'   => $fer_id,
                                'vac_qte'  => $vac_qte
                            ]);
                            session()->flash('success_message', 'Succès - Opération reçue !');
                        }
                    }
                    break;

                case 'D': // Suppression
                    $id = $request->input('vac_id');
                    $deleted = Vaccination::destroy($id);
                    
                    if ($deleted) {
                        session()->flash('success_message', 'Succès - Opération reçue !');
                    } else {
                        session()->flash('error_message', 'Échec - Opération non reçue !');
                    }
                    break;

                case 'V': // Validation d'état + déduction de stock
                    $id = $request->input('vac_id');
                    $vaccination = Vaccination::find($id);

                    if ($vaccination && $vaccination->vac_etat == 0) {
                        $produit = Produit::find($vaccination->pro_id);

                        if ($produit && $produit->pro_stock >= $vaccination->vac_qte) {
                            DB::transaction(function () use ($vaccination, $produit) {
                                // 1. Mettre à jour l'état de la vaccination à "Validé"
                                $vaccination->update(['vac_etat' => 1]);
                                
                                // 2. Mettre à jour le stock du produit associé
                                $produit->decrement('pro_stock', $vaccination->vac_qte);
                            });
                            session()->flash('success_message', 'Succès - Vaccination validée et stock déduit !');
                        } else {
                            session()->flash('error_message', 'Échec - Stock insuffisant pour valider cette opération !');
                        }
                    } else {
                        session()->flash('error_message', 'Échec - Opération introuvable ou déjà validée !');
                    }
                    break;
            }
        }

        // --- CHARGEMENT DES DONNÉES POUR LA VUE ---

        // Liste principale des vaccinations pour la ferme actuelle
        $vaccinations = Vaccination::with(['produit', 'poulailler', 'veterinaire'])
            ->where('fer_id', $fer_id)
            ->get();

        // Gestion de la sélection si mode édition / suppression / détail demandé
        $vaccinationSelectionnee = null;
        if (in_array($request->input('form'), ['M', 'S', 'V'])) {
            $vaccinationSelectionnee = Vaccination::with(['produit', 'poulailler', 'veterinaire'])
                ->find($request->input('vac_id'));
        }

        // Listes filtrées par type "0" (Vaccins) pour alimenter vos selects du formulaire
        $produits = Produit::where('pro_etat', 0)->where('fer_id', $fer_id)->orderBy('pro_nom')->get();
        $veterinaires = Veterinaire::where('fer_id', $fer_id)->where('vtr_etat', 0)->orderBy('vtr_nom')->get();
        $poulaillers = Poulailler::where('fer_id', $fer_id)->where('poul_etat', 0)->orderBy('poul_nom')->get();

        // --- STATISTIQUES (Filtres Temporels avec Carbon) ---
        $now = Carbon::now();

        $vac_ans = Vaccination::where('fer_id', $fer_id)->where('vac_etat', 1)
            ->whereYear('created_at', $now->year)->get();

        $vac_mois = Vaccination::where('fer_id', $fer_id)->where('vac_etat', 1)
            ->whereMonth('created_at', $now->month)->get();

        $vac_week = Vaccination::where('fer_id', $fer_id)->where('vac_etat', 1)
            ->whereBetween('created_at', [$now->startOfWeek()->format('Y-m-d'), $now->endOfWeek()->format('Y-m-d')])->first();

        $vac_day = Vaccination::where('fer_id', $fer_id)->where('vac_etat', 1)
            ->whereDate('created_at', Carbon::today())->first();

        // Redirection vers le dossier "Vaccinations" 
        return view('Vaccinations.index', compact(
            'vaccinations', 'vaccinationSelectionnee', 'produits', 
            'veterinaires', 'poulaillers', 'vac_ans', 'vac_mois', 'vac_week', 'vac_day', 'fer_id'
        ));
    }
}