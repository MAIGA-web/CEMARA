<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Production;
use App\Models\Produire;
use App\Models\Produit;

class ProductionController extends Controller
{
    /**
     * Gère l'affichage général et les filtres
     */
    public function index(Request $request)
    {
        $fer_id = session('fer_id') ?? auth()->user()->fer_id;
        $prd_id = $request->input('prd_id');
        // On récupère l'action passée dans l'URL (ex: 'edit')
        $action = $request->input('action');

        $prd = null;
        $produires = collect();

        // Si un ID est fourni, on charge la production à modifier
        if ($prd_id) {
            $prd = Production::with('poulailler')->find($prd_id);
            $produires = Produire::with('produit')
                ->where('prodc_id', $prd_id)
                ->get();
        }

        $productions = Production::where('fer_id', $fer_id)
            ->orderBy('created_at', 'DESC')
            ->get();

        $poulailler = DB::table('poulaillers')
            ->where('poul_etat', 0)
            ->where('fer_id', $fer_id)
            ->get();

        $produits = Produit::where('fer_id', $fer_id)->where('pro_etat', 1)->get();

        $max_oeuf_stock = Produit::where('pro_etat', 3)
            ->where('fer_id', $fer_id)
            ->sum(DB::raw('CAST(pro_stock AS NUMERIC)'));

        // On passe bien la variable $action à la vue
        return view('Productions.index', compact(
            'prd',
            'produires',
            'productions',
            'poulailler',
            'produits',
            'max_oeuf_stock',
            'action'
        ));
    }

    public function create($id = null)
    {
        $fer_id = session('fer_id') ?? auth()->user()->fer_id;

        // Sécurité : Si $id est nul, on regarde si un ID a été passé en query string (?4 ou ?id=4)
        if (!$id) {
            // Récupère l'ID si l'URL est de la forme ?id=4 ou prend la première clé du tableau si c'est juste ?4
            $id = request()->query('id') ?? key(request()->query());
        }

        // Si on a un ID valide et numérique, on cherche la production, sinon une nouvelle instance vide
        $prd = ($id && is_numeric($id)) ? Production::where('fer_id', $fer_id)->findOrFail($id) : new Production();

        // Récupère uniquement les poulaillers actifs de la ferme actuelle
        $poulailler = DB::table('poulaillers')
            ->where('poul_etat', 0)
            ->where('fer_id', $fer_id)
            ->get();

        // Récupération des poussins (pro_etat = 1)
        $produits = Produit::where('fer_id', $fer_id)->where('pro_etat', 1)->get();

        // Calcul du stock maximum d'œufs disponibles
        $max_oeuf_stock = \App\Models\Produit::where('pro_etat', 3)
            ->where('fer_id', $fer_id)
            ->sum(DB::raw('CAST(pro_stock AS NUMERIC)'));

        return view('Productions.create', compact('prd', 'poulailler', 'produits', 'max_oeuf_stock'));
    }

    /**
     * Traitement des actions (Ancien Switch $_POST['emp'])
     */
    public function storeAction(Request $request)
    {
        $action = $request->input('emp');
        $fer_id = session('fer_id') ?? auth()->user()->fer_id;

        switch ($action) {

            // ==========================================
            // CASE 'C' : CRÉATION D'UNE PRODUCTION + PRÉ-REMPLISSAGE
            // ==========================================
            case 'C':
                $request->validate([
                    'nbr_ouef'   => 'required|integer|min:1',
                    'poul_id'    => 'required',
                    'prodc_dure' => 'required|integer',
                ]);

                // 1. Création de la fiche de reproduction principale
                $production = Production::create([
                    'nbr_ouef'   => $request->nbr_ouef,
                    'poul_id'    => $request->poul_id,
                    'prodc_dure' => $request->prodc_dure,
                    'fer_id'     => $fer_id,
                    'prodc_etat' => 0 // Non validé au départ
                ]);

                // 2. Récupérer TOUS les types de poussins de cette ferme (pro_etat = 1)
                $poussinsDisponibles = Produit::where('pro_etat', 1)
                    ->where('fer_id', $fer_id)
                    ->get();

                // 3. Insérer automatiquement chaque type de poussin dans la table 'produires' avec Qte = 0
                foreach ($poussinsDisponibles as $poussin) {
                    Produire::create([
                        'prodc_id' => $production->id,
                        'pro_id'   => $poussin->id,
                        'prdr_qte' => 0, // Quantité par défaut à 0, modifiable ensuite
                        'fer_id'   => $fer_id
                    ]);
                }

                // On redirige vers la liste en ouvrant directement cette production
                return redirect()->route('production.index', ['prd_id' => $production->id])
                    ->with('success_message', 'Production créée ! Vos types de poussins ont été pré-remplis à 0.');

                // ==========================================
                // CASE 'U' : MISE À JOU DE LA PRODUCTION PRINCIPALE
                // ==========================================
            case 'U':
                $request->validate(['prd_id' => 'required|exists:productions,id']);

                $production = Production::findOrFail($request->prd_id);
                $production->update([
                    'poul_id'    => $request->poul_id,
                    'prodc_dure' => $request->prodc_dure,
                    'nbr_ouef'   => $request->nbr_ouef,
                ]);

                return redirect()->route('production.index', ['prd_id' => $production->id])
                    ->with('success_message', 'Production mise à jour !');

                // ==========================================
                // CASE 'I' (ou 'C' secondaire) : AJOUTER UN RÉSULTAT DE PRODUCTION (POUSSINS OBTENUS)
                // ==========================================
                // Formulaire pour lier un type de poussin et sa quantité obtenue à cette éclosion
            case 'INS':
                $request->validate([
                    'prd_id'   => 'required|exists:productions,id',
                    'pro_id'   => 'required|exists:produits,id',
                    'prdr_qte' => 'required|integer|min:0'
                ]);

                // On vérifie si ce type de poussin est déjà enregistré pour cette éclosion
                $existe = Produire::where('prodc_id', $request->prd_id)
                    ->where('pro_id', $request->pro_id)
                    ->first();

                if ($existe) {
                    // Si oui, on met juste à jour la quantité obtenue
                    $existe->update(['prdr_qte' => $request->prdr_qte]);
                } else {
                    // Sinon, on crée la ligne de résultat
                    Produire::create([
                        'prodc_id' => $request->prd_id,
                        'pro_id'   => $request->pro_id,
                        'prdr_qte' => $request->prdr_qte,
                        'fer_id'   => $fer_id
                    ]);
                }

                return redirect()->route('production.index', ['prd_id' => $request->prd_id])
                    ->with('success_message', 'Résultat enregistré.');

                // ==========================================
                // CASE 'PU' : MODIFIER LE RÉSULTAT DIRECTEMENT
                // ==========================================
                // ==========================================
                // CASE 'PU' : MODIFIER LE RÉSULTAT DE NAISSANCE (AVEC VÉRIFICATION)
                // ==========================================
            case 'PU':
                $request->validate([
                    'cprd_id'  => 'required|exists:produires,id',
                    'prdr_qte' => 'required|integer|min:0'
                ]);

                // 1. Récupérer la ligne de résultat qu'on veut modifier
                $produire = Produire::findOrFail($request->cprd_id);

                // 2. Récupérer la production parente pour connaître le nombre d'œufs total
                $production = Production::findOrFail($produire->prodc_id);

                // Sécurité : Si la production est déjà clôturée, on bloque
                if ($production->prodc_etat == 1) {
                    return redirect()->back()->with('error_message', 'Impossible de modifier, cette production est déjà validée.');
                }

                $nouvelleQte = (int) $request->prdr_qte;

                // 3. Calculer le total des poussins des AUTRES lignes de cette même production
                $totalAutresPoussins = Produire::where('prodc_id', $production->id)
                    ->where('id', '!=', $produire->id) // On exclut la ligne en cours de modification
                    ->sum('prdr_qte');

                // 4. Vérifier si le nouveau total global dépasse le nombre d'œufs mis au départ
                if (($totalAutresPoussins + $nouvelleQte) > (int) $production->nbr_ouef) {
                    return redirect()->back()->with(
                        'error_message',
                        "Erreur : Le total des poussins (" . ($totalAutresPoussins + $nouvelleQte) . ") ne peut pas dépasser le nombre d'œufs total mis en incubation (" . $production->nbr_ouef . ")."
                    );
                }

                // 5. Si tout est bon, on met à jour la quantité
                $produire->update(['prdr_qte' => $nouvelleQte]);

                return redirect()->route('production.index', ['prd_id' => $produire->prodc_id, 'tab' => 'pills-profile'])
                    ->with('success_message', 'Quantité de poussins mise à jour avec succès.');

                // ==========================================
                // CASE 'PRV' : VALIDATION FINALE ET INCORPORATION AUX STOCKS
                // ==========================================
            case 'PRV':
                if ($request->input('valider') === 'Oui') {
                    $id = $request->input('prd_id');

                    DB::transaction(function () use ($id, $fer_id) {
                        $production = Production::findOrFail($id);

                        // 1. Clôturer la production
                        $production->update(['prodc_etat' => 1]);

                        // 2. SOUSTRAIRE les œufs initiaux du stock global d'œufs (pro_etat = 3)
                        $produitOeuf = Produit::where('pro_etat', 3)->where('fer_id', $fer_id)->first();
                        if ($produitOeuf) {
                            $stockOeufsActuel = (int) $produitOeuf->pro_stock;
                            $produitOeuf->update([
                                'pro_stock' => $stockOeufsActuel - (int) $production->nbr_ouef
                            ]);
                        }

                        // 3. AJOUTER les poussins réellement obtenus aux stocks respectifs
                        // On va chercher toutes les lignes de résultats validées dans 'produires'
                        $resultats = Produire::where('prodc_id', $id)->get();

                        foreach ($resultats as $res) {
                            $poussinStock = Produit::find($res->pro_id);
                            if ($poussinStock) {
                                $stockPoussinsActuel = (int) $poussinStock->pro_stock;
                                // On ajoute la quantité finale mesurée et saisie par l'utilisateur
                                $poussinStock->update([
                                    'pro_stock' => $stockPoussinsActuel + (int) $res->prdr_qte
                                ]);
                            }
                        }
                    });

                    return redirect()->route('production.index')->with('success_message', 'Production clôturée ! Les stocks de poussins ont été mis à jour.');
                }
                break;

            // ==========================================
            // CASE 'D' : SUPPRESSION D'UNE PRODUCTION
            // ==========================================
            case 'D':
                if ($request->input('valider') === 'Oui') {
                    $id = $request->input('prd_id');
                    Produire::where('prodc_id', $id)->delete(); // Supprime les résultats associés
                    Production::destroy($id);

                    return redirect()->route('production.index')->with('success_message', 'Fiche supprimée.');
                }
                break;
        }

        return redirect()->back()->with('error_message', 'Opération échouée.');
    }
}
