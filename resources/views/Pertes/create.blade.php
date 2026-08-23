@extends('layout.header')
@section('contenu')
<div id="right-panel" class="right-panel">
    <div class="content">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-lg-1"></div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <strong>{{ isset($perteEnEdition) && $perteEnEdition->id ? 'Modifier la Fiche de Perte' : 'Nouvelle Fiche de Perte' }}</strong>
                        </div>
                        <div class="card-body card-block">
                            <form method="POST" action="{{ url('/Pertes') }}">
                                @csrf
                                <input type="hidden" name="emp" value="{{ isset($perteEnEdition) ? 'U' : 'C' }}">
                                <input type="hidden" name="per_id" value="{{ $perteEnEdition->id ?? '' }}">
                                <input type="hidden" name="fer_id" value="{{ session('fer_id') }}">

                                <div class="form-group">
                                    <label class="form-control-label">Poulailler Concerne</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-home"></i></div>
                                        <select name="poul_id" class="form-control" required>
                                            <option value="">-- Sélectionner le poulailler --</option>
                                            @foreach($poulaillers as $poul)
                                                <option value="{{ $poul->id }}" {{ (isset($perteEnEdition) && $perteEnEdition->poul_id == $poul->id) ? 'selected' : '' }}>
                                                    {{ $poul->poul_nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                @if(isset($perteEnEdition))
                                    <div class="form-group">
                                        <label class="form-control-label">Date</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="datetime-local" name="per_date" value="{{ $perteEnEdition->created_at->format('Y-m-d\TH:i') }}" class="form-control" required>
                                        </div>
                                    </div>
                                @endif

                                <div class="modal-footer">
                                    <a href="{{ url('/Pertes') }}" class="btn btn-sm btn-danger">Annuler</a>
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