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
                                <strong>{{ isset($collectionEnEdition) && $collectionEnEdition->id ? 'Modifier la Fiche de Collecte' : 'Nouvelle Session de Ramassage' }}</strong>
                            </div>
                            <div class="card-body card-block">
                                @if (session('error_message'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>Erreur !</strong> {{ session('error_message') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                <form method="POST" action="{{ url('/Collections') }}">
                                    @csrf

                                    <input type="hidden" name="emp"
                                        value="{{ isset($collectionEnEdition) ? 'U' : 'C' }}">
                                    <input type="hidden" name="col_id" value="{{ $collectionEnEdition->id ?? '' }}">
                                    <input type="hidden" name="fer_id" value="{{ session('fer_id') }}">

                                    <div class="form-group">
                                        <label class="form-control-label">Poulailler Collecté</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-home"></i></div>
                                            <select name="poul_id" class="form-control" required>
                                                <option value="">-- Sélectionner le poulailler --</option>
                                                @foreach ($poulaillers as $poul)
                                                    <option value="{{ $poul->id }}"
                                                        {{ isset($collectionEnEdition) && $collectionEnEdition->poul_id == $poul->id ? 'selected' : '' }}>
                                                        {{ $poul->poul_nom }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    @if (isset($collectionEnEdition))
                                        <div class="form-group">
                                            <label class="form-control-label">Modifier Date / Heure</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                <input type="datetime-local" name="col_date"
                                                    value="{{ $collectionEnEdition->created_at->format('Y-m-d\TH:i') }}"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="modal-footer">
                                        <a href="{{ url('/Collections') }}" class="btn btn-sm btn-danger">Annuler</a>
                                        <button type="submit" name="valider" value="Valider"
                                            class="btn btn-sm btn-primary">Initialiser la fiche</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-lg-3"></div> --}}
                </div>
            </div>
        </div>
    </div>
@endsection
