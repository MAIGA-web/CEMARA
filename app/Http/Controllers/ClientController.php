<?php

namespace App\Http\Controllers;

use App\Models\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    //
    public function clients()
    {
        $client = Client::all();
        // $clientss= new Client;
        return view('Clients.index')->with(compact('client'));
    }
    public function create(Request $request, $id = null)
    {
           $fer_id = session('fer_id') ?? (auth()->user()->fer_id ?? null);
        if ($id == "") {
            $client = new Client;
        } else {
            $client = Client::findOrFail($id);
        }
        if ($request->isMethod('post')) {
            $donne = $request->all();
            $validator = Validator::make($donne, [
                'cl_nom'     => 'required',
                'cl_prenom'  => 'required',
                'cl_adresse' => 'required',
                'cl_sexe'    => 'required|in:M,F',
                'cl_tel' => [
                    'required' , 'max:11',
                    // Si $id est nul (ajout), il vérifie l'unicité normalement
                    // Si $id existe (edit), il ignore cet ID dans la vérification
                    Rule::unique('clients', 'cl_tel')->ignore($id)->where('fer_id',$fer_id)
                ],
            ], [
                'cl_nom.required'    => 'Le nom est obligatoire',
                'cl_prenom.required' => 'Le prénom est obligatoire',
                'cl_adresse.required' => 'L\'adresse est obligatoire',
                'cl_sexe.required'   => 'Le sexe est obligatoire',
                'cl_tel.required'    => 'Le téléphone est obligatoire',
                'cl_tel.unique'      => 'Ce numéro de téléphone est déjà utilisé par un autre client.',
            ]);
            if ($validator->fails()) {
                return  redirect()->back()->withErrors($validator)->withInput();
            }
            $client->cl_nom = $donne['cl_nom'];
            $client->cl_prenom = $donne['cl_prenom'];
            $client->cl_adresse = $donne['cl_adresse'];
            $client->cl_sexe = $donne['cl_sexe'];
            $client->cl_tel = $donne['cl_tel'];
            $client->cl_etat = $donne['cl_etat'] ?? true;
            $client->fer_id = session('fer_id') ?? auth()->user()->fer_id;
            // dd($request->all());
            $client->save();
            if ($id == "") {
                $message = "Le client a été ajouté avec succès !";
            } else {
                $message = "Les informations du client ont été mises à jour.";
            }
            return redirect('/Clients')->with('success_message', $message);
        }
        return view('Clients.create', compact('client'));
    }
    public function suppression($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
        if ($id) $message_suprimer = "Les informations du client ont été.";
        return redirect('Clients')->with('success_delete', $message_suprimer);
    }
}
