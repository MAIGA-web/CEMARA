<?php

namespace App\Http\Controllers;

use App\Models\Mode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ModeController extends Controller
{
    public function modes()
    {
        $mode = Mode::all();
        return view('Modes.index')->with(compact('mode'));
    }
    public function create(Request $request, $id = null)
    {
        $fer_id = session('fer_id') ?? auth()->user()->fer_id;

        if ($id == "") {
            $mode = new Mode();
        } else {
            $mode = Mode::findOr($id);
        }
        if ($request->isMethod('post')) {
            $donne = $request->all();
            $validator = Validator::make($donne, [
                'mod_nom' =>[ 'required',
                    Rule::unique('modes', 'mod_nom')->ignore($mode->id)->where('fer_id',$fer_id)
            ],
            ], [
                'mod_nom.required' => 'Le champ est obligatoire',
                'mod_nom.unique' => 'Ce mode existe déjax',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $mode->mod_nom = $donne['mod_nom'];
            $mode->fer_id = session('fer_id') ?? auth()->user()->fer_id;
            $mode->save();

            if ($id == "") {
                $message = "Le mode a été ajouté avec succès !";
            } else {
                $message = "Les informations du mode ont été mises à jour.";
            }
            return redirect('/Modes')->with('success_message', $message);
        }
        return view('Modes.create')->with(compact('mode'));
    }
    public function suppression($id)
    {
        $mode = Mode::find($id);
        $mode->delete();
        if ($id) $message_suprimer = "Les informations du produit ont été ";
        return redirect('Modes')->with('success_delete', $message_suprimer);
    }
}
