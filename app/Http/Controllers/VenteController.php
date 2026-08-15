<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Client;
use App\Models\Mode;
use App\Models\Vendre;
use App\Models\Produit;
use App\Models\Ferme;
use Spipu\Html2Pdf\Html2Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VenteController extends Controller
{
    // 1. Affichage principal (Liste + Détails si ID fourni)
    public function index(Request $request)
    {
        $fer_id = session('fer_id') ?? (auth()->user()->fer_id ?? null);

        $venteSelectionnee = null;
        $produitsVendus = [];
        $historique = [];

        if ($request->has('details')) {
            $venteSelectionnee = Vente::with('client')->find($request->details);
            if ($venteSelectionnee) {
                $produitsVendus = Vendre::with('produit')
                    ->where('vte_id', $venteSelectionnee->id)
                    ->get();
                $historique = \App\Models\Paiement::where('vte_id', $venteSelectionnee->id)->get();

                if ($venteSelectionnee->fer_id) {
                    $fer_id = $venteSelectionnee->fer_id;
                }
            }
        }

        // if (!$fer_id) {
        //     $fer_id = Vente::value('fer_id') ?? DB::table('fermes')->value('id');
        // }

        $ventes = Vente::where('fer_id', $fer_id)->with('client')->latest()->get();
        $produits = Produit::where('fer_id', $fer_id)->get();
        $moes = Mode::where('fer_id', $fer_id)->get();
        // dd($fer_id, $modes->toArray());
        return view('Ventes.index', compact(
            'ventes',
            'venteSelectionnee',
            'produitsVendus',
            'historique',
            'produits',
            'moes'
        ));
    }

    // 2. Création ou modification d'une Vente (Client / État)
    public function createOrUpdate(Request $request, $id = null)
    {
        $vente = $id ? Vente::findOrFail($id) : new Vente();

        if ($request->isMethod('post')) {
            $request->validate([
                'cl_id' => 'required|exists:clients,id',
            ], [
                'cl_id.required' => 'Le choix du client est obligatoire.',
            ]);

            $vente->cl_id = $request->cl_id;
            $vente->vte_etat = $request->has('vte_etat') ? true : false;
            $vente->fer_id = session('fer_id') ?? auth()->user()->fer_id;
            $vente->save();

            return redirect()->route('ventes.index', ['details' => $vente->id])
                ->with('success_message', 'Vente enregistrée avec succès.');
        }

        $clients = Client::all();
        return view('Ventes.create_vente', compact('vente', 'clients'));
    }

    // 3. Ajouter un produit à une vente
    public function storeProduit(Request $request)
    {
        $request->validate([
            'vte_id' => 'required|exists:ventes,id',
            'pro_id' => 'required|exists:produits,id',
            'vdr_pu' => 'required|numeric|min:0',
            'vdr_qte' => 'required|integer|min:1',
        ]);

        // 1. Vérification du stock (global ou par ferme)
        $stock = DB::table('produits')
            ->where('id', $request->pro_id)
            ->selectRaw('SUM(pro_stock::numeric) as stock_actuel')
            ->value('stock_actuel') ?? 0;

        if ($request->vdr_qte > $stock) {
            return redirect()->back()
                ->with('error_message', "Erreur : Le Stock saisi (" . number_format($request->vdr_qte, 0, ',', ' ') . ") 
                dépasse le reste du stock actuel (" . number_format($stock, 0, ',', ' ') . " ).")
                ->withInput();
        }

        // 2. RECHERCHE : Est-ce que ce produit est déjà dans cette vente ?
        $vendre = Vendre::where('vte_id', $request->vte_id)
            ->where('pro_id', $request->pro_id)
            ->first();

        if ($vendre) {
            // MISE À JOUR : On ajoute la nouvelle quantité à l'existante
            $vendre->vdr_qte += $request->vdr_qte;
            // Optionnel : mettre à jour le prix unitaire si celui-ci a changé
            $vendre->vdr_pu = $request->vdr_pu;
        } else {
            // CRÉATION : Nouvelle ligne si le produit n'y est pas encore
            $vendre = new Vendre();
            $vendre->vte_id = $request->vte_id;
            $vendre->pro_id = $request->pro_id;
            $vendre->vdr_pu = $request->vdr_pu;
            $vendre->vdr_qte = $request->vdr_qte;
            $vendre->fer_id = session('fer_id') ?? auth()->user()->fer_id;
        }

        $vendre->save();

        return redirect()->route('ventes.index', ['details' => $request->vte_id, 'tab' => 'pills-profile']);
    }

    // 4. Supprimer un produit d'une vente
    public function deleteProduit($id)
    {
        $vendre = Vendre::findOrFail($id);
        $vte_id = $vendre->vte_id;
        $vendre->delete();

        return redirect()->route('ventes.index', ['details' => $vte_id, 'tab' => 'pills-profile'])
            ->with('success_message', 'Produit retiré de la vente.');
    }

    // 5. Supprimer une vente entière
    public function delete($id)
    {
        $vente = Vente::findOrFail($id);
        // Supprime aussi les produits associés pour éviter les erreurs d'intégrité
        Vendre::where('vte_id', $vente->id)->delete();
        $vente->delete();

        return redirect()->route('ventes.index')->with('success_message', 'Vente supprimée.');
    }
    // 6. Charger le formulaire de modification d'un produit (AJAX ou Page)
    public function editProduit($id)
    {
        $vendre = Vendre::findOrFail($id);
        $produits = Produit::all();
        $venteSelectionnee = Vente::find($vendre->vte_id);

        // On retourne une vue spécifique pour la modification
        return view('Ventes.partials.edit_produit', compact('vendre', 'produits', 'venteSelectionnee'));
    }

    // 7. Enregistrer la modification
    public function updateProduit(Request $request, $id)
    {
        $request->validate([
            'vdr_pu' => 'required|numeric|min:0',
            'vdr_qte' => 'required|integer|min:1',
            'pro_id' => 'required|exists:produits,id',
        ]);

        $vendre = Vendre::findOrFail($id);
        $vendre->update([
            'pro_id' => $request->pro_id,
            'vdr_pu' => $request->vdr_pu,
            'vdr_qte' => $request->vdr_qte,
        ]);

        return redirect()->route('ventes.index', ['details' => $vendre->vte_id, 'tab' => 'pills-profile'])
            ->with('success_message', 'Produit mis à jour avec succès.');
    }

    public function storePaiement(Request $request)
    {
        $request->validate([
            'vte_id' => 'required|exists:ventes,id',
            'pa_payer' => 'required|numeric|min:1',
            'mod_id' => 'required'
        ]);

        // 1. Calculer le montant total de la vente
        $totalVente = DB::table('vendres')
            ->where('vte_id', $request->vte_id)
            ->selectRaw('SUM(vdr_pu * vdr_qte) as total')
            ->value('total') ?? 0;

        // 2. Calculer ce qui a déjà été payé
        $dejaPaye = \App\Models\Paiement::where('vte_id', $request->vte_id)->sum('pa_payer');

        $resteAPayer = $totalVente - $dejaPaye;

        // 3. Vérification : le nouveau paiement ne doit pas dépasser le reste
        if ($request->pa_payer > $resteAPayer) {
            return redirect()->back()
                ->with('error_message', "Erreur : Le montant saisi (" . number_format($request->pa_payer, 0, ',', ' ') . " F) dépasse le reste à payer (" . number_format($resteAPayer, 0, ',', ' ') . " F).")
                ->withInput();
        }

        // 4. Si c'est bon, on enregistre
        $paiement = new \App\Models\Paiement();
        $paiement->vte_id = $request->vte_id;
        $paiement->pa_payer = $request->pa_payer;
        $paiement->mod_id = $request->mod_id;
        $paiement->pa_etat = false;
        $paiement->fer_id = session('fer_id') ?? auth()->user()->fer_id;
        $paiement->save();

        return redirect()->route('ventes.index', ['details' => $request->vte_id, 'tab' => 'pills-paie'])
            ->with('success_message', 'Paiement enregistré.');
    }

    // Modifier un paiement (Formulaire)
public function editPaiement($id)
{
    $paiement = \App\Models\Paiement::findOrFail($id);
    $vente = Vente::findOrFail($paiement->vte_id);

    if ($paiement->pa_etat) {
        return redirect()->back()->with('error_message', 'Impossible de modifier un paiement sur une vente validée.');
    }

    $fer_id = session('fer_id') ?? auth()->user()->fer_id;
    $modes = Mode::where('fer_id', $fer_id)->get();

    return view('Ventes.partials.edit_paiement', compact('paiement', 'modes', 'vente'));
}

    // Update du paiement
    public function updatePaiement(Request $request, $id)
    {
        $paiement = \App\Models\Paiement::findOrFail($id);

        $request->validate([
            'pa_payer' => 'required|numeric|min:1',
            'mod_id' => 'required'
        ]);

        // Utilise l'ID de la vente qui est déjà enregistré dans le paiement
        $vte_id = $paiement->vte_id;

        // 1. Calcul du total réel de la vente
        $totalVente = DB::table('vendres')
            ->where('vte_id', $vte_id)
            ->selectRaw('SUM(vdr_pu * vdr_qte) as total')
            ->value('total') ?? 0;

        // 2. Calcul de ce qui est payé par les AUTRES (on exclut ce paiement-ci)
        $payeParAutres = \App\Models\Paiement::where('vte_id', $vte_id)
            ->where('id', '!=', $id) // CRITIQUE : Ne pas se compter soi-même
            ->sum('pa_payer');

        // 3. Le  reste à payer autorisé pour ce paiement
        $maxAutorisePourCeChamp = $totalVente - $payeParAutres;

        if ($request->pa_payer > $maxAutorisePourCeChamp) {
            return redirect()->back()
                ->with('error_message', 'Le montant dépasse le total de la vente (' . number_format($maxAutorisePourCeChamp, 0, ',', ' ') . ' F max).')
                ->withInput();
        }

        // 4. Mise à jour
        $paiement->update([
            'pa_payer' => $request->pa_payer,
            'mod_id' => $request->mod_id,
        ]);

        return redirect()->route('ventes.index', ['details' => $vte_id, 'tab' => 'pills-paie'])
            ->with('success_message', 'Paiement mis à jour.');
    }
    // Valider Paiement
    public function validerPaiement($id)
    {
        $paiement = \App\Models\Paiement::findOrFail($id);
        $paiement->pa_etat = true; // On valide le paiement
        $paiement->save();

        return redirect()->back()->with('success_message', 'Le paiement a été validé et verrouillé.');
    }

    // Supprimer un paiement
    public function deletePaiement($id)
    {
        $paiement = \App\Models\Paiement::findOrFail($id);
        $vente = Vente::find($paiement->vte_id);

        if ($paiement->pa_etat) {
            return redirect()->back()->with('error_message', 'Action impossible : vente déjà validée.');
        }

        $paiement->delete();
        return redirect()->back()->with('success_message', 'Paiement supprimé.');
    }

public function valider($id)
{
    $vente = Vente::findOrFail($id);

    if ($vente->vte_etat) {
        return redirect()->back()->with('error_message', 'Cette vente est déjà validée.');
    }

    DB::transaction(function () use ($vente) {
        $produitsVendus = Vendre::where('vte_id', $vente->id)->get();

        foreach ($produitsVendus as $item) {
            $produit = Produit::lockForUpdate()->find($item->pro_id);

            if ($produit) {
                if ($produit->pro_stock < $item->vdr_qte) {
                    throw new \Exception("Stock insuffisant pour le produit : " . $produit->pro_nom);
                }

                $produit->pro_stock -= $item->vdr_qte;
                $produit->save();
            }
        }

        $vente->vte_etat = true;
        $vente->save();
    });

    return redirect()->route('ventes.index', ['details' => $id])
        ->with('success_message', 'Vente validée et stock mis à jour avec succès !');
}

public function recuPaiement($id)
    {
        // 1. Charger la vente AVEC ses paiements et son client
        $vente = \App\Models\Vente::with(['client', 'paiements'])->findOrFail($id);

        // Récupérer la ferme via la session, sinon la vente, sinon l'utilisateur
        $fermeId = session('fer_id') ?? $vente->fer_id ?? auth()->user()->fer_id;
        $ferme = Ferme::find($fermeId);

        // --- GESTION ET VÉRIFICATION SÉCURISÉE DU LOGO POUR HTML2PDF ---
        $logoPath = null;
        if ($ferme && $ferme->logo) {
            $checkPath = storage_path('app/public/logos/' . $ferme->logo);
            
            // Si le fichier existe physiquement sur le disque du serveur
            if (file_exists($checkPath)) {
                $logoPath = $checkPath;
            }
        }

        // Si l'image n'existe pas, on bascule sur un logo par défaut dans public/ ou null
        if (!$logoPath) {
            $defaultLogo = public_path('assets/images/logo.png'); // Ajustez selon votre projet
            $logoPath = file_exists($defaultLogo) ? $defaultLogo : null;
        }

        // 2. Récupérer les produits
        $produits = \App\Models\Vendre::with('produit')->where('vte_id', $id)->get();

        $totalPaye = $vente->paiements->where('pa_etat', true)->sum('pa_payer');
        $resteAPayer = $vente->vte_total - $totalPaye;

        $content = view('Ventes.recu', [
            'vente' => $vente,
            'paiements' => $vente->paiements,
            'produits' => $produits,
            'resteAPayer' => $resteAPayer,
            'totalPaye' => $totalPaye,
            'ferme' => $ferme,
            'logoPath' => $logoPath // On passe le chemin vérifié à la vue
        ])->render();

        if (ob_get_contents()) ob_end_clean();
        ob_start();

        try {
            $html2pdf = new Html2Pdf('P', 'A4', 'fr');
            $html2pdf->writeHTML($content);
            $html2pdf->output('Recu_Vente_' . $id . '.pdf', 'I');
            exit;
        } catch (\Spipu\Html2Pdf\Exception\Html2PdfException $e) {
            return $e->getMessage();
        }
    }
}
