@extends('layout.header')
@section('contenu')
    <div id="right-panel" class="right-panel">
        <div class="content">
            <div class="animated fadeIn">
                <div class="row">

                    {{-- <div class="col-4"></div> --}}
                    <div class="col-12">
                        <div class="card">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="card-header">
                                @if ($fournisseur['id'])
                                    Modification fournisseurs
                                @else
                                    Nouveau fournisseurs
                                @endif
                            </div>
                            <div class="card-body card-block">
                                <form method="POST" enctype="multipart/form-data" {{-- action="{{ (isset($fournisseurs) && $fournisseur->exists) ? url('/Fournisseurs/add-edit/'.$fournisseurs->id) : url('/Fournisseurs/add-edit') }}"> --}}
                                    action="{{ $fournisseur->id ? url('/Fournisseurs/add-edit/' . $fournisseur->id) : url('/Fournisseurs/add-edit') }}">
                                    @csrf
                                    <div class="form-group ">
                                        <label class=" form-control-label">Nom</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                            <input type="text" class="form-control" name="four_nom" placeholder="Nom"
                                                @if ($fournisseur['four_nom']) value="{{ $fournisseur['four_nom'] }}"
											@else value="{{ old('four_nom') }}" @endif>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <label class=" form-control-label">Prénom</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                            <input type="text" class="form-control" name="four_prenom"
                                                placeholder="Prénom"
                                                @if ($fournisseur['four_prenom']) value="{{ $fournisseur['four_prenom'] }}"
											@else value="{{ old('four_prenom') }}" @endif>
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <label class=" form-control-label">Adresse</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-home"></i></div>
                                            <input type="text" class="form-control" name="four_adresse"
                                                placeholder="Adresse"
                                                @if ($fournisseur['four_adresse']) value="{{ $fournisseur['four_adresse'] }}"
											@else value="{{ old('four_adresse') }}" @endif>
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <label class=" form-control-label">Tel</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-phone"></i></div>
                                            <input type="text" class="form-control" name="four_tel" placeholder="Tel "
                                                @if ($fournisseur['four_tel']) value="{{ $fournisseur['four_tel'] }}"
											@else value="{{ old('four_tel') }}" @endif
                                                maxlength="8">
                                        </div>
                                    </div>

                                    @if ($fournisseur->id)
                                        <div class="form-group">
                                            <label class="form-control-label">État du compte</label>
                                            <div class="input-group">
                                                <div class="radio-inline">
                                                    <input type="radio" name="four_etat" value="1"
                                                        {{ $fournisseur->four_etat ? 'checked' : '' }}>
                                                    <label class="ml-1 mr-3">Actif</label>

                                                    <input type="radio" name="four_etat" value="0"
                                                        {{ !$fournisseur->four_etat ? 'checked' : '' }}>
                                                    <label class="ml-1">Inactif</label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="form-group ">
                                        {{-- <div class="input-group-addon"><i class="fa fa-user"></i></div> --}}
                                        <label class=" form-control-label">Sexe</label>
                                        <div class="checkbox">

                                            <input type="radio" name="four_sexe" value="M"
                                                {{ $fournisseur->four_sexe == 'M' ? 'checked' : '' }}>
                                            <label class=" form-control-label">Masculin</label>
                                            <div class="checkbox">
                                                {{-- <div class="input-group-addon"><i class="fa fa-user"></i></div> --}}
                                                <input type="radio" name="four_sexe" value="F"
                                                    {{ $fournisseur->four_sexe == 'F' ? 'checked' : '' }}>
                                                <label>
                                                    Féminin
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="{{ url('/Fournisseurs') }}" class="btn btn-danger mb-1">
                                            Annuler
                                        </a>
                                        <button type="submit" class="btn btn-primary">Valider</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
