<?php

namespace App\Http\Controllers;

use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MatiereController extends Controller
{
    public function matieres()
    {
        // Utilisation du pluriel $matieres pour plus de clarté
        $matieres = Matiere::all();
        return view('Matieres.index', compact('matieres'));
    }

    public function create(Request $request, $id = null)
    {
        // Utilise find() au lieu de firstOrFail si l'id peut être null au départ
        $matiere = $id ? Matiere::findOrFail($id) : new Matiere();

        if ($request->isMethod('post')) {
            $donne = $request->all();

            $validator = Validator::make($donne, [
                'ma_nom' => 'required',
                'ma_type' => [
                    'required',
                    // Correction ici : Rattachement correct de la règle unique
                    Rule::unique('matieres', 'ma_type')->ignore($matiere->id)
                ],
                'ma_stock' => 'required',
                // 'ma_etat' => 'required',
            ], [
                'ma_nom.required' => 'Le champ nom est obligatoire.',
                'ma_type.unique' => 'Ce type de matière existe déjà.',
                'ma_type.required' => 'Le champ type est obligatoire.',
                'ma_stock.required' => 'Le champ stock est obligatoire.',
                // 'ma_etat.required' => 'Le champ état est obligatoire.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $matiere->ma_nom = $donne['ma_nom'];
            $matiere->ma_type = $donne['ma_type'];
            $matiere->ma_stock = $donne['ma_stock'];
           $matiere->ma_etat = $donne['ma_etat'] ?? true;
            $matiere->fer_id = session('fer_id') ?? auth()->user()->fer_id;

            $matiere->save();

            $message = $id ? "Les informations ont été mises à jour." : "La matière a été ajoutée avec succès !";
            
            return redirect('/Matieres')->with('success_message', $message);
        }

        return view('Matieres.create', compact('matiere'));
    }

    public function suppression($id)
    {
        $matiere = Matiere::findOrFail($id);
        $matiere->delete();

        return redirect('Matieres')->with('success_delete', "La matière a été supprimée avec succès.");
    }
}