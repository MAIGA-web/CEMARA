@extends('layout.header')
@section('contenu')

<div id="right-panel" class="right-panel">
    <div class="content">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-lg-3"></div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <strong>{{ isset($alimentationEnEdition) && $alimentationEnEdition->id ? 'Modifier la Fiche d\'Alimentation' : 'Nouvelle Fiche d\'Alimentation' }}</strong>
                        </div>
                        <div class="card-body card-block">
                            <form method="POST" action="{{ url('/Alimentations') }}">
                                @csrf
                                
                                {{-- Aiguillage pour le contrôleur --}}
                                <input type="hidden" name="emp" value="{{ isset($alimentationEnEdition) ? 'U' : 'C' }}">
                                <input type="hidden" name="alm_id" value="{{ $alimentationEnEdition->id ?? '' }}">
                                <input type="hidden" name="fer_id" value="{{ session('fer_id') }}">

                                {{-- Choix du poulailler (Seule information requise à la création d'une fiche) --}}
                                <div class="form-group">
                                    <label class="form-control-label">Poulailler Cible</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-home"></i></div>
                                        <select name="poul_id" class="form-control" required>
                                            <option value="">-- Sélectionner le poulailler --</option>
                                            @foreach($poulaillers as $poul)
                                                <option value="{{ $poul->id }}" {{ (isset($alimentationEnEdition) && $alimentationEnEdition->poul_id == $poul->id) ? 'selected' : '' }}>
                                                    {{ $poul->poul_nom }} (Capacité: {{ $poul->poul_capacite }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- La date s'affiche uniquement si on est en mode modification --}}
                                @if(isset($alimentationEnEdition))
                                    <div class="form-group">
                                        <label class="form-control-label">Date d'enregistrement</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="datetime-local" name="alm_date" value="{{ $alimentationEnEdition->created_at->format('Y-m-d\TH:i') }}" class="form-control" required>
                                        </div>
                                    </div>
                                @endif

                                <div class="modal-footer">
                                    <a href="{{ url('/Alimentations') }}" class="btn btn-sm btn-danger">Annuler</a>
                                    <button type="submit" name="valider" value="Valider" class="btn btn-sm btn-primary">Créer et continuer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3"></div>
            </div>
        </div>
    </div>
</div>
@endsection