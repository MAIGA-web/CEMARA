<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transformation;
use App\Models\Transformer;
use App\Models\Matiere;
use App\Models\Produit;

class TransformationController extends Controller
{
    public function index(Request $request)
    {
        $fer_id = session('fer_id') ?? auth()->user()->fer_id ?? 1;
        $trans_id = $request->input('trans_id');

        $transformation_selectionnee = null;
        $liaisons_transformer = collect();

        if ($trans_id) {
            $transformation_selectionnee = Transformation::with('matiere')
                ->where('fer_id', $fer_id)
                ->find($trans_id);

            if ($transformation_selectionnee) {
                $liaisons_transformer = Transformer::with('produit')
                    ->where('trans_id', $trans_id)
                    ->get();
            }
        }

        $transformations = Transformation::with('matiere')
            ->where('fer_id', $fer_id)
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('Transformations.index', compact(
            'transformations',
            'transformation_selectionnee',
            'liaisons_transformer',
            'fer_id'
        ));
    }

    public function create()
    {
        $fer_id = session('fer_id') ?? auth()->user()->fer_id ?? 1;
        $transformation = new Transformation();
        
        $matieres = Matiere::where('fer_id', $fer_id)
            ->where('ma_stock', '>', 0)
            ->get();

        return view('Transformations.create', compact('transformation', 'matieres'));
    }

    public function edit($id)
    {
        $fer_id = session('fer_id') ?? auth()->user()->fer_id ?? 1;
        $transformation = Transformation::where('fer_id', $fer_id)->findOrFail($id);

        if ($transformation->trans_etat == 1) {
            return redirect()->route('transformations.index')
                ->with('error_message', 'Impossible de modifier une transformation déjà validée.');
        }

        $matieres = Matiere::where('fer_id', $fer_id)->get();
        return view('Transformations.create', compact('transformation', 'matieres'));
    }

    public function store(Request $request)
    {
        $action = $request->input('emp');
        $fer_id = session('fer_id') ?? auth()->user()->fer_id ?? 1;

        switch ($action) {

            // ==========================================
            // CASE 'C' : CRÉATION AVEC VÉRIFICATION DU STOCK
            // ==========================================
            case 'C':
                $request->validate([
                    'ma_id'     => 'required|exists:matieres,id',
                    'trans_qte' => 'required|numeric|min:0.01',
                ]);

                // 1. Vérification du stock réel disponible
                $matiere = Matiere::where('id', $request->ma_id)->where('fer_id', $fer_id)->firstOrFail();

                if ($request->trans_qte > $matiere->ma_stock) {
                    return redirect()->back()
                        ->with('error_message', "Erreur : La quantité saisie (" . number_format($request->trans_qte, 2, ',', ' ') . ") dépasse le stock disponible de matière première (" . number_format($matiere->ma_stock, 2, ',', ' ') . ").")
                        ->withInput();
                }

                $transformation = DB::transaction(function () use ($request, $fer_id) {

                    $trans = Transformation::create([
                        'ma_id'      => $request->ma_id,
                        'trans_qte'  => $request->trans_qte,
                        'fer_id'     => $fer_id,
                        'trans_etat' => 0
                    ]);

                    $produitsFinis = Produit::where('pro_etat', 2)
                        ->where('fer_id', $fer_id)
                        ->get();

                    $nbProduits = $produitsFinis->count();

                    if ($nbProduits > 0) {
                        // Rendement total = 97% de la quantité injectée, réparti équitablement entre les produits
                        $rendementParProduit = ($request->trans_qte * 0.97) / $nbProduits;

                        foreach ($produitsFinis as $produit) {
                            Transformer::create([
                                'trans_id' => $trans->id,
                                'pro_id'   => $produit->id,
                                'trme_qte' => $rendementParProduit,
                                'fer_id'   => $fer_id
                            ]);
                        }
                    }

                    return $trans;
                });

                return redirect()->route('transformations.index', ['trans_id' => $transformation->id])
                    ->with('success_message', 'Transformation créée ! Le rendement total à 97% a été généré.');

            // ==========================================
            // CASE 'U' : MISE À JOUR DE LA QUANTITÉ INJECTÉE
            // ==========================================
            case 'U':
                $request->validate([
                    'trans_id'  => 'required|exists:transformations,id',
                    'trans_qte' => 'required|numeric|min:0.01',
                    'ma_id'     => 'required|exists:matieres,id'
                ]);

                $transformation = Transformation::findOrFail($request->trans_id);

                if ($transformation->trans_etat == 1) {
                    return redirect()->back()->with('error_message', 'Action impossible, cette transformation est verrouillée.');
                }

                // Vérification du stock
                $matiere = Matiere::where('id', $request->ma_id)->where('fer_id', $fer_id)->firstOrFail();
                if ($request->trans_qte > $matiere->ma_stock) {
                    return redirect()->back()
                        ->with('error_message', "Erreur : La quantité saisie (" . number_format($request->trans_qte, 2, ',', ' ') . ") dépasse le stock disponible (" . number_format($matiere->ma_stock, 2, ',', ' ') . ").")
                        ->withInput();
                }

                DB::transaction(function () use ($transformation, $request, $fer_id) {
                    $transformation->update([
                        'ma_id'     => $request->ma_id,
                        'trans_qte' => $request->trans_qte,
                    ]);

                    $produitsFinisCount = Transformer::where('trans_id', $transformation->id)->count();

                    if ($produitsFinisCount > 0) {
                        $rendementParProduit = ($request->trans_qte * 0.97) / $produitsFinisCount;
                        Transformer::where('trans_id', $transformation->id)->update([
                            'trme_qte' => $rendementParProduit
                        ]);
                    }
                });

                return redirect()->route('transformations.index', ['trans_id' => $transformation->id])
                    ->with('success_message', 'Transformation et rendements mis à jour !');

            // ==========================================
            // CASE 'PU' : AJUSTEMENT MANUEL DU RENDEMENT
            // ==========================================
            case 'PU':
                $request->validate([
                    'trm_id'   => 'required|exists:transformers,id',
                    'trme_qte' => 'required|numeric|min:0'
                ]);

                $transformer = Transformer::findOrFail($request->trm_id);
                $transformation = Transformation::findOrFail($transformer->trans_id);

                if ($transformation->trans_etat == 1) {
                    return redirect()->back()->with('error_message', 'Cette opération est déjà validée et verrouillée.');
                }

                $transformer->update(['trme_qte' => $request->trme_qte]);

                return redirect()->route('transformations.index', ['trans_id' => $transformer->trans_id, 'tab' => 'pills-products'])
                    ->with('success_message', 'Rendement ajusté avec succès.');

            // ==========================================
            // CASE 'PRV' : VALIDATION FINALE & MOUVEMENTS DE STOCK
            // ==========================================
            case 'PRV':
                $id = $request->input('trans_id');

                DB::transaction(function () use ($id, $fer_id) {
                    $transformation = Transformation::findOrFail($id);

                    if ($transformation->trans_etat == 1) {
                        return;
                    }

                    // Double vérification au moment de déduire le stock
                    $matiere = Matiere::where('id', $transformation->ma_id)->where('fer_id', $fer_id)->lockForUpdate()->first();

                    if ($matiere) {
                        if ($matiere->ma_stock < $transformation->trans_qte) {
                            throw new \Exception("Stock insuffisant pour la matière première : " . $matiere->ma_nom);
                        }

                        $matiere->update([
                            'ma_stock' => (float)$matiere->ma_stock - (float)$transformation->trans_qte
                        ]);
                    }

                    $rendements = Transformer::where('trans_id', $id)->get();
                    foreach ($rendements as $rendement) {
                        $produitFini = Produit::where('id', $rendement->pro_id)->where('fer_id', $fer_id)->first();
                        if ($produitFini) {
                            $produitFini->update([
                                'pro_stock' => (float)$produitFini->pro_stock + (float)$rendement->trme_qte
                            ]);
                        }
                    }

                    $transformation->update(['trans_etat' => 1]);
                });

                return redirect()->route('transformations.index', ['trans_id' => $id])
                    ->with('success_message', 'Transformation validée ! Stocks mis à jour.');

            // ==========================================
            // CASE 'D' : SUPPRESSION
            // ==========================================
            case 'D':
                $id = $request->input('trans_id');
                $transformation = Transformation::findOrFail($id);

                if ($transformation->trans_etat == 1) {
                    return redirect()->back()->with('error_message', 'Impossible de supprimer une transformation déjà validée.');
                }

                DB::transaction(function () use ($id) {
                    Transformer::where('trans_id', $id)->delete();
                    Transformation::destroy($id);
                });

                return redirect()->route('transformations.index')->with('success_message', 'Transformation supprimée.');
        }

        return redirect()->back()->with('error_message', 'Action non reconnue.');
    }
}