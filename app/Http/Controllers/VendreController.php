<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Vendre;
use App\Models\Vente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VendreController extends Controller
{
public function vendres($id)
{
    $vente = Vente::with('client')->findOrFail($id);
    $vendre = Vendre::with('produit')->where('vte_id', $id)->get();
    
    return view('Ventes.Vendre.vente', compact('vendre', 'vente'));
}

    public function create(Request $request, $id = null)
    {
        // 1. Initialisation pour le mode GET
        if ($request->isMethod('get')) {
            // Ici $id est l'ID de la Vente (vte_id)
            $vente = Vente::with('client')->findOrFail($id);
            
            // On crée un objet vide pour le formulaire (évite les erreurs undefined)
            $vendre = new Vendre();
            // On pré-assigne l'ID de la vente pour qu'il soit utilisé dans le champ caché du formulaire
            $vendre->vte_id = $id; 
            
            $produit = Produit::all();
            
            return view('Ventes.Vendre.create', compact('vendre', 'vente', 'produit'));
        }

        // 2. Traitement du formulaire (POST)
        if ($request->isMethod('post')) {
            $donne = $request->all();

            $validator = Validator::make($donne, [
                'vdr_pu'  => 'required|numeric',
                'vdr_qte' => 'required|integer',
                'vte_id'  => 'required', // L'ID de la vente parente
                'pro_id'  => 'required',
            ], [
                'vdr_pu.required'  => 'Le prix unitaire est obligatoire',
                'vdr_qte.required' => 'La quantité est obligatoire',
                'pro_id.required'  => 'Veuillez choisir un produit',
                'vte_id.required'  => 'Lien avec la vente manquant',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // On crée une NOUVELLE ligne dans la table 'vendre'
            $vendre = new Vendre();
            $vendre->vdr_pu  = $donne['vdr_pu'];
            $vendre->vdr_qte = $donne['vdr_qte'];
            $vendre->vte_id  = $donne['vte_id'];
            $vendre->pro_id  = $donne['pro_id']; 
            $vendre->save();

            // redirection vers la liste ou la vue de la vente avec un message de succès
       return redirect()->route('vendre.liste', ['id' => $vendre->vte_id])
                 ->with('success_message', 'Produit ajouté avec succès à la vente');
        }
        
    }
}