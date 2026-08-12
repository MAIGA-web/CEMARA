<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProduitController extends Controller
{
    public function produits(){
        $produit=Produit::all();
        return view('Produits.index')->with(compact('produit'));
    }
    public function create(Request $request,$id=null){
        $fer_id = session('fer_id') ?? auth()->user()->fer_id ?? 1;

        if($id==""){
            $produit= new Produit();
        }
        else{
            $produit=Produit::findOr($id);
        }
if($request->isMethod('post')){
            $donne=$request->all();
            
            $validator=Validator::make($donne,[
                'pro_nom' => [
                    'required',
                    Rule::unique('produits', 'pro_nom')
                        ->ignore($produit->id) // Important pour la modification
                        ->where(function($query) use ($fer_id){
                            return $query->where('fer_id', $fer_id);
                        })
                ],
                'pro_type' => [
                    'required',
                ],
                'pro_stock' => 'required',
                'pro_etat'  => 'required',
            ],[
                'pro_nom.unique'     => 'Le produit existe déjà dans cette ferme.',
                'pro_nom.required'   => 'Le champ nom est obligatoire.',
                'pro_type.required'  => 'Le champ type est obligatoire.',
                'pro_stock.required' => 'Le champ stock est obligatoire.',
                'pro_etat.required'  => 'Le champ état est obligatoire.',
            ]);

            if($validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Si c'est un nouvel ajout, ne pas oublier d'associer la ferme
            if (!$produit->exists) {
                $produit->fer_id = $fer_id;
            }

            $produit->pro_nom   = $donne['pro_nom'];
            $produit->pro_type  = $donne['pro_type'];
            $produit->pro_stock = $donne['pro_stock'];
            $produit->pro_etat  = $donne['pro_etat'];

            $produit->save();

            if($id == "") {
                $message = "Le produit a été ajouté avec succès !";
            } else {
                $message = "Les informations du produit ont été mises à jour.";
            }
            return redirect('/Produits')->with('success_message', $message);
        }
        return view('Produits.create')->with(compact('produit'));
    }
    public function suppression($id){
        $produit=Produit::find($id);
        $produit->delete();
        if($id) $message_suprimer = "Les informations du produit ont été ";
        return redirect('Produits')->with('success_delete',$message_suprimer);
    }
}
