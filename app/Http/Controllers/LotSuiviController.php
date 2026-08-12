<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\SuiviJournalier;
use App\Models\Poulailler;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LotSuiviController extends Controller
{
    /**
     * 1. Page principale : Liste des lots et détails du lot sélectionné
     */
    public function index(Request $request)
    {
        $fer_id = session('fer_id') ?? auth()->user()->fer_id ?? 1;
        $lot_id = $request->input('lot_id');

        // Récupérer tous les lots de la ferme
        $lots = Lot::with(['poulailler', 'produit'])
            ->where('fer_id', $fer_id)
            ->orderBy('lot_actif', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->get();

        $lotSelectionne = null;
        $suivisJournaliers = collect();

        // Si l'utilisateur clique sur un lot pour voir les détails/suivis
        if ($lot_id) {
            $lotSelectionne = Lot::with('poulailler')
                ->where('fer_id', $fer_id)
                ->find($lot_id);

            if ($lotSelectionne) {
                // Charger l'historique des suivis du lot (du plus récent au plus ancien)
                $suivisJournaliers = SuiviJournalier::where('lot_id', $lot_id)
                    ->orderBy('suivi_date', 'DESC')
                    ->get();
            }
        }

        // Liste des poulaillers pour le formulaire de création de lot
        $produits = Produit::where('pro_etat', 1)->where('fer_id', $fer_id)->get();
        $poulaillers = Poulailler::where('fer_id', $fer_id)->get();

        return view('Lots.index', compact('lots', 'lotSelectionne', 'suivisJournaliers', 'poulaillers', 'produits'));
    }

    /**
     * 2. Enregistrer un NOUVEAU Lot de poussins
     */
    public function storeLot(Request $request)
    {
        $fer_id = session('fer_id') ?? auth()->user()->fer_id ?? 1;

        $request->validate([
            'lot_code' => [
                'required',
                'string',
                \Illuminate\Validation\Rule::unique('lots', 'lot_code')->where(function ($query) use ($fer_id) {
                    return $query->where('fer_id', $fer_id);
                }),
            ],
            'poul_id'          => 'required|exists:poulaillers,id',
            'pro_id'       => 'required|exists:produits,id',
            'lot_qte_initiale' => 'required|integer|min:1',
            'lot_date_arrivee' => 'required|date',
            'duree_elevage'     => 'required|integer|min:1',
            'origine'          => 'required|string',
        ]);

        // Calcul automatique de la date de sortie prévue
        $dateArrivee = Carbon::parse($request->lot_date_arrivee);
        $dateSortiePrevue = $dateArrivee->copy()->addDays((int)$request->duree_elevage)->format('Y-m-d');

        Lot::create([
            'fer_id'                 => $fer_id,
            'poul_id'                => $request->poul_id,
            'pro_id'                 => $request->pro_id,
            'lot_code'               => $request->lot_code,
            'lot_qte_initiale'       => $request->lot_qte_initiale,
            'lot_date_arrivee'       => $request->lot_date_arrivee,
            'origine'                => $request->origine,
            'lot_date_sortie_prevue' => $dateSortiePrevue,
            'lot_actif'              => true
        ]);

        return redirect()->route('lots.index')
            ->with('success_message', 'Le lot de poussins a été enregistré avec succès !');
    }

    public function updateLot(Request $request, $id)
    {
        $fer_id = session('fer_id') ?? auth()->user()->fer_id ?? 1;
        $lot = Lot::where('fer_id', $fer_id)->findOrFail($id);

        $request->validate([
            'lot_code'         => 'required|string|unique:lots,lot_code,' . $id,
            'poul_id'          => 'required|exists:poulaillers,id',
            'pro_id'          => 'required|exists:produits,id',
            'origine'          => 'required|string|max:255',
            'lot_qte_initiale' => 'required|integer|min:1',
            'lot_date_arrivee' => 'required|date',
        ]);

        $lot->update([
            'lot_code'         => $request->lot_code,
            'poul_id'          => $request->poul_id,
            'pro_id'          => $request->pro_id,
            'origine'          => $request->origine,
            'lot_qte_initiale' => $request->lot_qte_initiale,
            'lot_date_arrivee' => $request->lot_date_arrivee,
        ]);

        return redirect()->route('lots.index', ['lot_id' => $lot->id])
            ->with('success_message', 'Le lot a été mis à jour avec succès.');
    }

    /**
     * 7. Supprimer définitivement un Lot et tout son historique
     */
    public function deleteLot($id)
    {
        $fer_id = session('fer_id') ?? auth()->user()->fer_id ?? 1;
        $lot = Lot::where('fer_id', $fer_id)->findOrFail($id);

        $lot->delete(); // Grâce au onDelete('cascade') de la migration, les suivis associés sautent aussi.

        return redirect()->route('lots.index')
            ->with('success_message', 'Le lot et tout son historique ont été supprimés.');
    }

    /**
     * 8. Modifier un rapport de suivi quotidien journalier
     */
    public function updateSuivi(Request $request, $id)
    {
        $fer_id = session('fer_id') ?? auth()->user()->fer_id ?? 1;
        $suivi = SuiviJournalier::where('fer_id', $fer_id)->findOrFail($id);
        $lot = Lot::where('fer_id', $fer_id)->findOrFail($suivi->lot_id);

        $request->validate([
            'suivi_date'           => 'required|date',
            'morts_jour'           => 'required|integer|min:0',
            'consommation_aliment' => 'required|numeric|min:0',
            'etat_sante'           => 'required|string|max:50',
            'observations'         => 'nullable|string'
        ]);

        // Recalculer le reste vivants virtuel (sans compter l'ancien enregistrement de ce jour)
        $mortsSaufAujourdhui = $lot->suivis()->where('id', '!=', $id)->sum('morts_jour');
        $resteVivantsVirtuel = $lot->lot_qte_initiale - $mortsSaufAujourdhui;

        if ($request->morts_jour > $resteVivantsVirtuel) {
            return redirect()->back()->with('error_message', "Action impossible : le nombre de morts dépasse l'effectif restant.");
        }

        $suivi->update([
            'suivi_date'           => $request->suivi_date,
            'morts_jour'           => $request->morts_jour,
            'consommation_aliment' => $request->consommation_aliment,
            'etat_sante'           => $request->etat_sante,
            'observations'         => $request->observations
        ]);

        return redirect()->route('lots.index', ['lot_id' => $lot->id])
            ->with('success_message', 'Le rapport quotidien a été mis à jour.');
    }

    /**
     * 3. Enregistrer le Suivi Journalier (Morts, aliment, santé)
     */
    public function storeSuivi(Request $request)
    {
        $fer_id = session('fer_id') ?? auth()->user()->fer_id ?? 1;

        $request->validate([
            'lot_id'               => 'required|exists:lots,id',
            'suivi_date'           => 'required|date',
            'morts_jour'           => 'required|integer|min:0',
            'consommation_aliment' => 'required|numeric|min:0',
            'etat_sante'           => 'required|string|max:50',
            'observations'         => 'nullable|string'
        ]);

        $lot = Lot::where('fer_id', $fer_id)->findOrFail($request->lot_id);

        // Sécurité : On empêche d'ajouter un suivi si le lot est déjà archivé/sorti
        if (!$lot->lot_actif) {
            return redirect()->back()->with('error_message', 'Impossible d\'ajouter un suivi sur un lot inactif.');
        }

        // Sécurité : Empêcher de déclarer plus de morts qu'il n'y a de poussins vivants restants
        if ($request->morts_jour > $lot->reste_vivants) {
            return redirect()->back()
                ->with('error_message', "Le nombre de morts saisis ({$request->morts_jour}) dépasse le nombre de poussins restants vivants ({$lot->reste_vivants}).")
                ->withInput();
        }

        // Vérifier si un suivi existe déjà pour cette date précise sur ce lot
        $dejaSaisi = SuiviJournalier::where('lot_id', $lot->id)
            ->whereDate('suivi_date', $request->suivi_date)
            ->exists();

        if ($dejaSaisi) {
            return redirect()->back()
                ->with('error_message', 'Un rapport de suivi a déjà été enregistré pour cette date. Modifiez le rapport existant.')
                ->withInput();
        }

        // Création du rapport quotidien
        SuiviJournalier::create([
            'fer_id'               => $fer_id,
            'lot_id'               => $request->lot_id,
            'suivi_date'           => $request->suivi_date,
            'morts_jour'           => $request->morts_jour,
            'consommation_aliment' => $request->consommation_aliment,
            'etat_sante'           => $request->etat_sante,
            'observations'         => $request->observations
        ]);

        return redirect()->route('lots.index', ['lot_id' => $lot->id])
            ->with('success_message', 'Le suivi journalier a été enregistré.');
    }

    /**
     * 4. Clôturer / Archiver un Lot (Fin de bande / Vente complète)
     */
    public function cloturerLot($id)
    {
        $fer_id = session('fer_id') ?? auth()->user()->fer_id ?? 1;

        // 1. Récupérer le lot avec son produit associé
        $lot = Lot::where('fer_id', $fer_id)->findOrFail($id);

        // Vérifier si le lot n'est pas déjà clôturé pour éviter les doubles injections de stock
        if (!$lot->lot_actif) {
            return redirect()->back()->with('error_message', 'Ce lot est déjà clôturé.');
        }

        // 2. Récupérer le produit lié à ce lot (ex: "Poulet de chair")
        $produit = \App\Models\Produit::where('fer_id', $fer_id)->find($lot->pro_id);

        if ($produit) {
            $quantiteAInjecter = (int)$lot->reste_vivants;

            $produit->pro_stock += $quantiteAInjecter;
            $produit->save();

            $messageStock = " et +{$quantiteAInjecter} insérés dans le stock de " . $produit->pro_nom;
        } else {
            $messageStock = " (Aucun produit correspondant trouvé pour le stock)";
        }

        // 3. Marquer le lot comme inactif
        $lot->update([
            'lot_actif' => false
        ]);

        return redirect()->route('lots.index', ['lot_id' => $lot->id])
            ->with('success_message', 'Le lot a été clôturé avec succès' . $messageStock . '.');
    }

    /**
     * 5. Supprimer un rapport de suivi journalier
     */
    public function deleteSuivi($id)
    {
        $fer_id = session('fer_id') ?? auth()->user()->fer_id ?? 1;
        $suivi = SuiviJournalier::where('fer_id', $fer_id)->findOrFail($id);
        $lot_id = $suivi->lot_id;

        $suivi->delete();

        return redirect()->route('lots.index', ['lot_id' => $lot_id])
            ->with('success_message', 'Rapport de suivi supprimé.');
    }
}
