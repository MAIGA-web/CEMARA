<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\Collecter;
use App\Models\Produit;
use App\Models\Poulailler;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{
    public function handleAction(Request $request)
    {
        $emp = $request->input('emp');
        $valider = $request->input('valider');
        $fer_id = $request->input('fer_id') ?? session('fer_id') ?? 1;
        $acc = $request->input('acc');

        if ($acc === 'C' ||$acc === 'U' || $request->is('Collections/add-edit*')) {
            $collectionEnEdition = $request->input('col_id') ? Collection::find($request->input('col_id')) : null;
            $poulaillers = Poulailler::where('fer_id', $fer_id)->where('poul_etat', 0)->orderBy('poul_nom')->get();

            return view('Collections.create', compact('poulaillers', 'collectionEnEdition'));
        }

        // --- TRAITEMENT DES SOUUMISSIONS DE FORMULAIRES (POST) ---
        if ($valider === 'Valider' || $valider === 'Oui') {
            switch ($emp) {

                case 'C': // Créer en-tête
                    Collection::create([
                        'poul_id'  => $request->input('poul_id'),
                        'fer_id'   => $fer_id,
                        'col_etat' => 0,
                    ]);
                    session()->flash('success_message', 'Fiche initialisée !');
                    return redirect('/Collections');

                case 'U': // Modifier en-tête
                    Collection::findOrFail($request->input('col_id'))->update([
                        'poul_id'    => $request->input('poul_id'),
                        'created_at' => $request->input('col_date'),
                    ]);
                    session()->flash('success_message', 'En-tête mis à jour !');
                    return redirect('/Collections?col_id=' . $request->input('col_id'));

                case 'D': // Supprimer
                    Collection::destroy($request->input('col_id'));
                    session()->flash('success_message', 'Fiche supprimée.');
                    return redirect('/Collections');

                    // ------------------ COMPOSANTS DIRECTS ------------------
                case 'CC':
                    Collecter::create([
                        'qte_ramasse'  => (int)$request->input('qte_ramasse'),
                        'qte_casse'    => (int)$request->input('qte_casse'),
                        'qte_consomme' => (int)$request->input('qte_consomme'),
                        'col_id'       => $request->input('col_id'),
                        'fer_id'       => $fer_id
                    ]);
                    session()->flash('success_message', 'Données de collecte ajoutées !');
                    break;

                case 'CU':
                    Collecter::findOrFail($request->input('coll_id'))->update([
                        'qte_ramasse'  => (int)$request->input('qte_ramasse'),
                        'qte_casse'    => (int)$request->input('qte_casse'),
                        'qte_consomme' => (int)$request->input('qte_consomme'),
                    ]);
                    session()->flash('success_message', 'Données mises à jour.');
                    break;

                case 'CD':
                    Collecter::destroy($request->input('coll_id'));
                    session()->flash('success_message', 'Ligne retirée.');
                    break;

                // ------------------ VALIDATION GLOBALE ------------------
                case 'CV':
                    $col_id = $request->input('col_id');
                    $collection = Collection::findOrFail($col_id);
                    $lignes = Collecter::where('col_id', $col_id)->get();

                    if ($lignes->isEmpty()) {
                        session()->flash('error_message', 'Aucune donnée saisie sur cette fiche !');
                        break;
                    }

                    $totalRamasse  = $lignes->sum('qte_ramasse');
                    $totalCasse    = $lignes->sum('qte_casse');
                    $totalConsomme = $lignes->sum('qte_consomme');

                    $re = $totalCasse + $totalConsomme;
                    $resteAVendre = $totalRamasse - $re;

                    if ($resteAVendre < 0) {
                        session()->flash('error_message', 'Erreur : Les pertes dépassent le total ramassé !');
                        break;
                    }

                    DB::transaction(function () use ($collection, $fer_id, $resteAVendre) {
                        $collection->update(['col_etat' => 1]);

                        $produitVente = Produit::where('fer_id', $fer_id)->where('pro_etat', 3)->first();
                        if ($produitVente) {
                            $produitVente->pro_stock = (int)$produitVente->pro_stock + $resteAVendre;
                            $produitVente->save();
                        }
                    });

                    session()->flash('success_message', "Validé ! Net versé au stock (État 3) : +{$resteAVendre} œufs.");
                    break;
            }
        }

        $collections = Collection::with(['poulailler'])->where('fer_id', $fer_id)->orderBy('created_at', 'desc')->get();
        // $col_id = $request->input('col_id') ?? ($collections->first()->id ?? null);
        $col_id = $request->input('col_id');
        $collectionSelectionnee = $col_id ? Collection::with(['poulailler'])->find($col_id) : null;
        $detailsCollecter = $col_id ? Collecter::where('col_id', $col_id)->get() : collect();

        $collectionEnEdition = ($acc === 'U') ? Collection::find($request->input('col_id')) : null;
        $collecterEnEdition = ($acc === 'AM') ? Collecter::find($request->input('coll_id')) : null;
        $poulaillers = Poulailler::where('fer_id', $fer_id)->where('poul_etat', 0)->orderBy('poul_nom')->get();

        return view('Collections.index', compact(
            'collections',
            'collectionSelectionnee',
            'detailsCollecter',
            'collectionEnEdition',
            'collecterEnEdition',
            'poulaillers',
            'acc'
        ));
    }
}
