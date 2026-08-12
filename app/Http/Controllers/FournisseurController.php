<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class FournisseurController extends Controller
{
    public function fournisseurs(){
        $fournisseur=Fournisseur::all();
        return view('Fournisseurs.index')->with(compact('fournisseur'));
    }
    public function create(Request $request,$id=null){
        if($id==""){
            $fournisseur=new Fournisseur;
        }
        else{
            $fournisseur=Fournisseur::findOr($id);
        }
        if($request->isMethod('post')){
            $donne=$request->all();
            $validator = Validator::make($donne,
            [
            'four_nom'     => 'required',
            'four_prenom'  => 'required',
            'four_adresse' => 'required',
            'four_sexe'    => 'required|in:M,F',
            'four_tel'=> ['required',
                // Si $id est nul (ajout), il vérifie l'unicité normalement
                // Si $id existe (edit), il ignore cet ID dans la vérification
                Rule::unique('fournisseurs', 'four_tel')->ignore($id)
                ],
            ],[
            'four_nom.required'    => 'Le nom est obligatoire',
            'four_prenom.required' => 'Le prénom est obligatoire',
            'four_adresse.required'=> 'L\'adresse est obligatoire',
            'four_sexe.required'   => 'Le sexe est obligatoire',
            'four_tel.required'    => 'Le téléphone est obligatoire',
            'four_tel.unique'      => 'Ce numéro de téléphone est déjà utilisé par un autre Fournisseur.',
            ]);
            if($validator->fails()){
            return  redirect()->back()->withErrors($validator)->withInput();
        }
        $fournisseur->four_nom=$donne['four_nom'];
        $fournisseur->four_prenom=$donne['four_prenom'];
        $fournisseur->four_adresse=$donne['four_adresse'];
        $fournisseur->four_sexe=$donne['four_sexe'];
        $fournisseur->four_tel=$donne['four_tel'];
        $fournisseur->four_etat=$donne['four_etat']?? true;
        $fournisseur->fer_id = session('fer_id') ?? auth()->user()->fer_id;
        // dd($request->all());
        $fournisseur->save();
        if($id == "") {
                $message = "Le fournisseur a été ajouté avec succès !";
            } else {
                $message = "Les informations du fournisseur ont été mises à jour.";
            }
        return redirect('/Fournisseurs')->with('success_message', $message);
        }
         return view('Fournisseurs.create', compact('fournisseur'));
    }
    public function suppression($id){
        $fournisseur=Fournisseur::find($id);
        $fournisseur->delete();
        if($id) $message_suprimer = "Les informations du fournisseur ont été";
        return redirect('Fournisseurs')->with('success_delete',$message_suprimer);
    }
}
