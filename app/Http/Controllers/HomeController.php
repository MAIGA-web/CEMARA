<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\Client;
use App\Models\Fournisseur;
use App\Models\Veterinaire;
use App\Models\Poulailler;
use App\Models\Vaccination;
use App\Models\Achat;
use App\Models\Vente;
use App\Models\Perte;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // 1. Récupération sécurisée de la ferme active (Session ou Profil)
        $fer_id = $request->input('fer_id') ?? session('fer_id') ?? auth()->user()->fer_id;

        // Repères temporels précis pour PostgreSQL
        $aujourdhui   = Carbon::today()->format('Y-m-d');
        $debutSemaine = Carbon::now()->startOfWeek()->format('Y-m-d H:i:s');
        $finSemaine   = Carbon::now()->endOfWeek()->format('Y-m-d H:i:s');
        $debutMois    = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $debutAnnee   = Carbon::now()->startOfYear()->format('Y-m-d H:i:s');

        // 2. Alertes Stocks Critiques (Stock <= 10 et Produit Actif)
        $produitsAlerte = Produit::where('fer_id', $fer_id)
            ->where('pro_stock', '<=', 10)
            ->get();

        // 3. Bloc Opérateurs (Compteurs de droite)
        $nbClients      = Client::where('fer_id', $fer_id)->count();
        $nbFournisseurs = Fournisseur::where('fer_id', $fer_id)->count();
        $nbVeterinaires = Veterinaire::where('fer_id', $fer_id)->count();
        $nbPoulaillers  = Poulailler::where('fer_id', $fer_id)->where('poul_etat', 0)->count();

        // 4. Bloc Stock Actuel des Produits (Liste du bas)
        $stocksProduits = Produit::where('fer_id', $fer_id)
            ->orderBy('pro_nom')
            ->get();

        // 5. Bloc Pertes (Calculs par périodes)
        // $pertes = Perte::where('fer_id', $fer_id)->get();
        // ==========================================
        // PERTES DU JOUR (DÉTAILLÉES)
        // ==========================================
        $perteJour = DB::table('pertes')
            ->join('perdres', 'pertes.id', '=', 'perdres.per_id')
            ->join('produits', 'produits.id', '=', 'perdres.pro_id')
            ->where('pertes.fer_id', $fer_id)
            ->where('per_etat', 't')
            ->whereDate('pertes.created_at', $aujourdhui)
            ->select(
                'produits.pro_nom', 
                'produits.pro_type', 
                DB::raw('SUM(CAST(perd_qte AS NUMERIC)) as total_qte')
            )
            ->groupBy('produits.id', 'produits.pro_nom', 'produits.pro_type')
            ->get();

        // ==========================================
        // PERTES DE LA SEMAINE (DÉTAILLÉES)
        // ==========================================
        $perteSemaine = DB::table('pertes')
            ->join('perdres', 'pertes.id', '=', 'perdres.per_id')
            ->join('produits', 'produits.id', '=', 'perdres.pro_id')
            ->where('pertes.fer_id', $fer_id)
            ->where('per_etat', 't')
            ->whereBetween('pertes.created_at', [$debutSemaine, $finSemaine])
            ->select(
                'produits.pro_nom', 
                'produits.pro_type', 
                DB::raw('SUM(CAST(perd_qte AS NUMERIC)) as total_qte')
            )
            ->groupBy('produits.id', 'produits.pro_nom', 'produits.pro_type')
            ->get();

        // ==========================================
        // PERTES DU MOIS (DÉTAILLÉES)
        // ==========================================
        $perteMois = DB::table('pertes')
            ->join('perdres', 'pertes.id', '=', 'perdres.per_id')
            ->join('produits', 'produits.id', '=', 'perdres.pro_id')
            ->where('pertes.fer_id', $fer_id)
            ->where('per_etat', 't')
            ->where('pertes.created_at', '>=', $debutMois)
            ->select(
                'produits.pro_nom', 
                'produits.pro_type', 
                DB::raw('SUM(CAST(perd_qte AS NUMERIC)) as total_qte')
            )
            ->groupBy('produits.id', 'produits.pro_nom', 'produits.pro_type')
            ->get();
            
        // ==========================================
        // PERTES DE L'ANNÉE (DÉTAILLÉES)
        // ==========================================
        $perteAn = DB::table('pertes')
            ->join('perdres', 'pertes.id', '=', 'perdres.per_id')
            ->join('produits', 'produits.id', '=', 'perdres.pro_id')
            ->where('pertes.fer_id', $fer_id)
            ->where('per_etat', 't')
            ->where('pertes.created_at', '>=', $debutAnnee)
            ->select(
                'produits.pro_nom', 
                'produits.pro_type', 
                DB::raw('SUM(CAST(perd_qte AS NUMERIC)) as total_qte')
            )
            ->groupBy('produits.id', 'produits.pro_nom', 'produits.pro_type')
            ->get();

        // 6. Bloc Vaccinations (Historiques par périodes)
        $vaccinations = Vaccination::with(['poulailler', 'veterinaire','produit'])
            ->where('fer_id', $fer_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $vaccinationsJour    = $vaccinations->filter(fn($v) => Carbon::parse($v->created_at)->isToday());
        $vaccinationsSemaine = $vaccinations->filter(fn($v) => Carbon::parse($v->created_at)->isCurrentWeek());
        $vaccinationsMois    = $vaccinations->filter(fn($v) => Carbon::parse($v->created_at)->isCurrentMonth());
        $vaccinationsAn      = $vaccinations->filter(fn($v) => Carbon::parse($v->created_at)->isCurrentYear());

        // =========================================================================
        // 7. BLOC FINANCES : REQUÊTES COMPATIBLES POSTGRESQL (DATES SÉCURISÉES)
        // =========================================================================

        // --- CALCUL DES VENTES ---
        $ventesJour = DB::table('ventes')
            ->join('vendres', 'ventes.id', '=', 'vendres.vte_id')
            ->join('paiements', 'ventes.id', '=', 'paiements.vte_id')
            ->where('ventes.fer_id', $fer_id)->where('pa_etat', 't')
            ->whereDate('paiements.created_at', $aujourdhui)
            ->sum(DB::raw('pa_payer'));

        $ventesSemaine = DB::table('ventes')
            ->join('vendres', 'ventes.id', '=', 'vendres.vte_id')
            ->join('paiements', 'ventes.id', '=', 'paiements.vte_id')
            ->where('ventes.fer_id', $fer_id)->where('pa_etat', 't')
            ->whereBetween('paiements.created_at', [$debutSemaine, $finSemaine])
            ->sum(DB::raw('pa_payer'));

        $ventesMois = DB::table('ventes')
            ->join('vendres', 'ventes.id', '=', 'vendres.vte_id')
            ->join('paiements', 'ventes.id', '=', 'paiements.vte_id')
            ->where('ventes.fer_id', $fer_id)->where('pa_etat', 't')
            ->where('paiements.created_at', '>=', $debutMois)
            ->sum(DB::raw('pa_payer'));

        $ventesAn = DB::table('ventes')
            ->join('vendres', 'ventes.id', '=', 'vendres.vte_id')
            ->join('paiements', 'ventes.id', '=', 'paiements.vte_id')
            ->where('ventes.fer_id', $fer_id)->where('pa_etat', 't')
            ->where('paiements.created_at', '>=', $debutAnnee)
            ->sum(DB::raw('pa_payer'));


        // --- CALCUL DES ACHATS ---
        $achatsJour = DB::table('achats')
            ->join('acheters', 'achats.id', '=', 'acheters.ac_id')
            ->join('reglements', 'achats.id', '=', 'reglements.ac_id')
            ->where('achats.fer_id', $fer_id)->where('re_etat', 't')
            ->whereDate('reglements.created_at', $aujourdhui)
            ->sum(DB::raw('re_mnt'));

        $achatsSemaine = DB::table('achats')
            ->join('acheters', 'achats.id', '=', 'acheters.ac_id')
            ->join('reglements', 'achats.id', '=', 'reglements.ac_id')

            ->where('achats.fer_id', $fer_id)->where('re_etat', 't')
            ->whereBetween('reglements.created_at', [$debutSemaine, $finSemaine])
            ->sum(DB::raw('re_mnt'));

        $achatsMois = DB::table('achats')
            ->join('acheters', 'achats.id', '=', 'acheters.ac_id')
            ->join('reglements', 'achats.id', '=', 'reglements.ac_id')

            ->where('achats.fer_id', $fer_id)->where('re_etat', 't')
            ->where('reglements.created_at', '>=', $debutMois)
            ->sum(DB::raw('re_mnt'));

        $achatsAn = DB::table('achats')
            ->join('acheters', 'achats.id', '=', 'acheters.ac_id')
            ->join('reglements', 'achats.id', '=', 'reglements.ac_id')

            ->where('achats.fer_id', $fer_id)->where('re_etat', 't')
            ->where('reglements.created_at', '>=', $debutAnnee)
            ->sum(DB::raw('re_mnt'));

        // 8. Envoi sécurisé de toutes les données à la vue
        return view('index', compact(
            'produitsAlerte',
            'nbClients',
            'nbFournisseurs',
            'nbVeterinaires',
            'nbPoulaillers',
            'stocksProduits',
            'vaccinationsJour',
            'vaccinationsSemaine',
            'vaccinationsMois',
            'vaccinationsAn',
            'perteJour',
            'perteSemaine',
            'perteMois',
            'perteAn',
            'achatsJour',
            'achatsSemaine',
            'achatsMois',
            'achatsAn',
            'ventesJour',
            'ventesSemaine',
            'ventesMois',
            'ventesAn'
        ));
    }
}
