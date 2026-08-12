<?php

namespace App\Http\Controllers;

use App\Models\Poulailler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PoulaillerController extends Controller
{
    public function poulaillers()
    {
        $poulailler = Poulailler::all();
        return view('Poulaillers.index')->with(compact('poulailler'));
    }
    public function create(Request $request, $id = null)
    {
        $fer_id = session('fer_id') ?? auth()->user()->fer_id;

        if ($id == "") {
            $poulailler = new Poulailler();
        } else {
            $poulailler = Poulailler::findOr($id);
        }
        if ($request->isMethod('post')) {
            $donne = $request->all();
            $validator = Validator::make($donne, [
                'poul_nom' => [
                    'required',
                    Rule::unique('poulaillers', 'poul_nom')->ignore($poulailler->id)->where('fer_id',$fer_id)
                ],
                'poul_capa' => 'required',
                'poul_empl' => 'required',
                'poul_etat' => 'nullable',
            ], [
                'poul_nom.required' => 'Le champ est obligatoir',
                'poul_nom.unique' => 'Le poulaillers existe déjà',
                'poul_capa.required' => 'Le champ capacitée est obligatoir',
                'poul_empl.required' => 'Le champ emplacement est obligatoir',

            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $poulailler->poul_nom = $donne['poul_nom'];
            $poulailler->poul_capa = $donne['poul_capa'];
            $poulailler->poul_empl = $donne['poul_empl'];
            $poulailler->poul_etat = $donne['poul_etat'] ?? 0;
            $poulailler->fer_id = session('fer_id') ?? auth()->user()->fer_id;


            $poulailler->save();

            if ($id == "") {
                $message = "Le poulailler a été ajouté avec succès !";
            } else {
                $message = "Les informations du poulailler ont été mises à jour.";
            }
            return redirect('/Poulaillers')->with('success_message', $message);
        }
        return view('Poulaillers.create')->with(compact('poulailler'));
    }
    public function suppression($id)
    {
        $poulailler = Poulailler::find($id);
        $poulailler->delete();

        if ($id) $message_suprimer = "Les informations du poulailler ont été";
        return redirect('/Poulaillers')->with('success_delete', $message_suprimer);
    }
}
