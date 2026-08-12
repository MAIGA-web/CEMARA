<?php

namespace App\Http\Controllers;

use App\Models\Veterinaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class VeterinaireController extends Controller
{
    public function veterinaires(){
        $veterinaire=Veterinaire::all();
        return view('Veterinaires.index')->with(compact('veterinaire'));
    }
    public function create(Request $request,$id=null){
        if($id==""){
            $veterinaire=new Veterinaire;
        }
        else{
            $veterinaire=Veterinaire::findOr($id);
        }
        if($request->isMethod('post')){
            $donne=$request->all();
            $validator = Validator::make($donne,
            [
            'vtr_nom'     => 'required',
            'vtr_prenom'  => 'required',
            'vtr_adresse' => 'required',
            'vtr_sexe'    => 'required|in:M,F',
            'vtr_tel'=> ['required',
                // Si $id est nul (ajout), il vérifie l'unicité normalement
                // Si $id existe (edit), il ignore cet ID dans la vérification
                Rule::unique('veterinaires', 'vtr_tel')->ignore($id)
                ],
            ],[
            'vtr_nom.required'    => 'Le nom est obligatoire',
            'vtr_prenom.required' => 'Le prénom est obligatoire',
            'vtr_adresse.required'=> 'L\'adresse est obligatoire',
            'vtr_sexe.required'   => 'Le sexe est obligatoire',
            'vtr_tel.required'    => 'Le téléphone est obligatoire',
            'vtr_tel.unique'      => 'Ce numéro de téléphone est déjà utilisé par un autre Veterinaire.',
            ]);
            if($validator->fails()){
            return  redirect()->back()->withErrors($validator)->withInput();
        }
        $veterinaire->vtr_nom=$donne['vtr_nom'];
        $veterinaire->vtr_prenom=$donne['vtr_prenom'];
        $veterinaire->vtr_adresse=$donne['vtr_adresse'];
        $veterinaire->vtr_sexe=$donne['vtr_sexe'];
        $veterinaire->vtr_tel=$donne['vtr_tel'];
        $veterinaire->vtr_etat=$donne['vtr_etat']?? true;
        $veterinaire->fer_id = session('fer_id') ?? auth()->user()->fer_id;

        // dd($request->all());
        $veterinaire->save();
        if($id == "") {
                $message = "Le Veterinaire a été ajouté avec succès !";
            } else {
                $message = "Les informations du Veterinaire ont été mises à jour.";
            }
        return redirect('/Veterinaires')->with('success_message', $message);
        }
         return view('Veterinaires.create', compact('veterinaire'));
    }
    public function suppression($id){
        $veterinaire=Veterinaire::find($id);
        $veterinaire->delete();
        if($id) $message_suprimer = "Les informations du Veterinaire ont été supprimé.";
        return redirect('Veterinaires')->with('success_delete',$message_suprimer);
    }
}
