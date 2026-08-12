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
    /**
     * Page principale (Index)
     */
    public function index(Request $request)
    {
        $fer_id = session('fer_id') ?? auth()->user()->fer_id ?? 1;
        $trans_id = $request->input('trans_id');

        $transformation_selectionnee = null;
        $liaisons_transformer = collect();

        // Si on consulte une transformation spécifique (Panneau de droite)
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

        // Liste des transformations à gauche (Filtrée par ferme)
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

    /**
     * Formulaire d'ajout
     */
    public function create()
    {
        $fer_id = session('fer_id') ?? auth()->user()->fer_id ?? 1;
        $transformation = new Transformation();
        
        // Récupère les matières premières disponibles en stock pour CETTE ferme
        $matieres = Matiere::where('fer_id', $fer_id)
            ->where('ma_stock', '>', 0)
            ->get();

        return view('Transformations.create', compact('transformation', 'matieres'));
    }

    /**
     * 🛑 LA MÉTHODE MANQUANTE : Formulaire de modification d'une transformation existante
     */
    public function edit($id)
    {
        $fer_id = session('fer_id') ?? auth()->user()->fer_id ?? 1;

        // 1. Récupérer la transformation à modifier (et vérifier qu'elle appartient à la bonne ferme)
        $transformation = Transformation::where('fer_id', $fer_id)->findOrFail($id);

        // Sécurité : On bloque la modification si elle est déjà validée
        if ($transformation->trans_etat == 1) {
            return redirect()->route('transformations.index')
                ->with('error_message', 'Impossible de modifier une transformation déjà validée et verrouillée.');
        }

        // 2. Récupérer les matières premières pour la liste déroulante
        $matieres = Matiere::where('fer_id', $fer_id)->get();

        // 3. Renvoyer la vue "create" (qui gère l'édition grâce au formulaire dynamique)
        return view('Transformations.create', compact('transformation', 'matieres'));
    }

    /**
     * Enregistrement et traitement des actions (Création, Modification, Validation, Suppression)
     */
    public function store(Request $request)
    {
        $action = $request->input('emp');
        $fer_id = session('fer_id') ?? auth()->user()->fer_id ?? 1;

        switch ($action) {

            // ==========================================
            // CASE 'C' : CRÉATION + RENDEMENT AUTOMATIQUE À 97%
            // ==========================================
            case 'C':
                $request->validate([
                    'ma_id'     => 'required|exists:matieres,id',
                    'trans_qte' => 'required|numeric|min:0.01',
                ]);

                $transformation = DB::transaction(function () use ($request, $fer_id) {

                    // 1. Création de la fiche de transformation principale
                    $trans = Transformation::create([
                        'ma_id'      => $request->ma_id,
                        'trans_qte'  => $request->trans_qte,
                        'fer_id'     => $fer_id,
                        'trans_etat' => 0
                    ]);

                    // 2. Récupérer les produits finis (pro_type = 2 pour transformation)
                    $produitsFinis = Produit::where('pro_etat', 2)
                        ->where('fer_id', $fer_id)
                        ->get();

                    // 3. Calcul du rendement automatique à 97%
                    $rendementGlobal = $request->trans_qte * 0.97;

                    foreach ($produitsFinis as $produit) {
                        Transformer::create([
                            'trans_id'  => $trans->id,
                            'pro_id'    => $produit->id,
                            'trme_qte'  => $rendementGlobal,
                            // 'trans_id' => $request->trans_qte,
                            'fer_id'    => $fer_id
                        ]);
                    }

                    return $trans;
                });

                return redirect()->route('transformations.index', ['trans_id' => $transformation->id])
                    ->with('success_message', 'Transformation créée ! Le rendement estimé à 97% a été généré automatiquement.');

            // ==========================================
            // CASE 'U' : MISE À JOU DE LA QUANTITÉ INJECTÉE
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

                DB::transaction(function () use ($transformation, $request) {
                    $transformation->update([
                        'ma_id'     => $request->ma_id,
                        'trans_qte' => $request->trans_qte,
                    ]);

                    // Recalcul automatique à 97%
                    $rendementGlobal = $request->trans_qte * 0.97;
                    Transformer::where('trans_id', $transformation->id)->update([
                        'trme_qte'  => $rendementGlobal,
                        'trans_id' => $request->trans_id
                    ]);
                });

                return redirect()->route('transformations.index', ['trans_id' => $transformation->id])
                    ->with('success_message', 'Transformation et rendements mis à jour !');

            // ==========================================
            // CASE 'PU' : AJUSTEMENT MANUEL D'UNE QUANTITÉ PRODUITE
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

                // Sauvegarde de l'ajustement manuel saisi par l'utilisateur
                $transformer->update(['trme_qte' => $request->trme_qte]);

                return redirect()->route('transformations.index', ['trans_id' => $transformer->trans_id, 'tab' => 'pills-products'])
                    ->with('success_message', 'Rendement ajusté avec succès.');

            // ==========================================
            // CASE 'PRV' : VALIDATION FINALE ET MOUVEMENTS DE STOCKS
            // ==========================================
            case 'PRV':
                $id = $request->input('trans_id');

                DB::transaction(function () use ($id, $fer_id) {
                    $transformation = Transformation::findOrFail($id);

                    if ($transformation->trans_etat == 1) {
                        return;
                    }

                    // 1. Changement d'état définitif
                    $transformation->update(['trans_etat' => 1]);

                    // 2. SOUSTRAIRE la matière première consommée du stock
                    $matiere = Matiere::where('id', $transformation->ma_id)->where('fer_id', $fer_id)->first();
                    if ($matiere) {
                        $matiere->update([
                            'ma_stock' => (float)$matiere->ma_stock - (float)$transformation->trans_qte
                        ]);
                    }

                    // 3. AJOUTER les produits transformés obtenus au stock de produits finis
                    $rendements = Transformer::where('trans_id', $id)->get();
                    foreach ($rendements as $rendement) {
                        $produitFini = Produit::where('id', $rendement->pro_id)->where('fer_id', $fer_id)->first();
                        if ($produitFini) {
                            $produitFini->update([
                                'pro_stock' => (float)$produitFini->pro_stock + (float)$rendement->trme_qte
                            ]);
                        }
                    }
                });

                return redirect()->route('transformations.index', ['trans_id' => $id])
                    ->with('success_message', 'Transformation validée ! Stocks mis à jour.');

            // ==========================================
            // CASE 'D' : SUPPRESSION D'UNE TRANSFORMATION
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