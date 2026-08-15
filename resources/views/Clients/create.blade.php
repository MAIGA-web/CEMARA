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
                            @if ($client['id'])
                                Modification client
                            @else
                                Nouveau client
                            @endif
                        </div>
                        <div class="card-body card-block">
                            <form method="POST" enctype="multipart/form-data" {{-- action="{{ (isset($clients) && $client->exists) ? url('/Clients/add-edit/'.$clients->id) : url('/Clients/add-edit') }}"> --}}
                                action="{{ $client->id ? url('/Clients/add-edit/' . $client->id) : url('/Clients/add-edit') }}">
                                @csrf
                                <div class="form-group ">
                                    <label class=" form-control-label">Nom</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                        <input type="text" class="form-control" name="cl_nom" placeholder="Nom"
                                            @if ($client['cl_nom']) value="{{ $client['cl_nom'] }}"
											@else value="{{ old('cl_nom') }}" @endif>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label class=" form-control-label">Prénom</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                        <input type="text" class="form-control" name="cl_prenom" placeholder="Prénom"
                                            @if ($client['cl_prenom']) value="{{ $client['cl_prenom'] }}"
											@else value="{{ old('cl_prenom') }}" @endif>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label class=" form-control-label">Adresse</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-home"></i></div>
                                        <input type="text" class="form-control" name="cl_adresse"
                                            placeholder="Adresse"
                                            @if ($client['cl_adresse']) value="{{ $client['cl_adresse'] }}"
											@else value="{{ old('cl_adresse') }}" @endif>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label class=" form-control-label">Tel</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-phone"></i></div>
                                        <input type="text" class="form-control" name="cl_tel" placeholder="Tel "
                                            @if ($client['cl_tel']) value="{{ $client['cl_tel'] }}"
											@else value="{{ old('cl_tel') }}" @endif
                                            maxlength="8">
                                    </div>
                                </div>

                                @if ($client->id)
                                    <div class="form-group">
                                        <label class="form-control-label">État du compte</label>
                                        <div class="input-group">
                                            <div class="radio-inline">
                                                <input type="radio" name="cl_etat" value="t"
                                                    {{ $client->cl_etat ? 'checked' : '' }}>
                                                <label class="ml-1 mr-3">Actif</label>

                                                <input type="radio" name="cl_etat" value="f"
                                                    {{ !$client->cl_etat ? 'checked' : '' }}>
                                                <label class="ml-1">Inactif</label>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="form-group ">
                                    {{-- <div class="input-group-addon"><i class="fa fa-user"></i></div> --}}
                                    <label class=" form-control-label">Sexe</label>
                                    <div class="checkbox">

                                        <input type="radio" name="cl_sexe" value="M"
                                            {{ $client->cl_sexe == 'M' ? 'checked' : '' }}>
                                        <label class=" form-control-label">Masculin</label>
                                        <div class="checkbox">
                                            {{-- <div class="input-group-addon"><i class="fa fa-user"></i></div> --}}
                                            <input type="radio" name="cl_sexe" value="F"
                                                {{ $client->cl_sexe == 'F' ? 'checked' : '' }}>
                                            <label>
                                                Féminin
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <a href="{{ url('/Clients') }}" class="btn btn-danger mb-1">
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
