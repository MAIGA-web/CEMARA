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
                                @if ($poulailler['id'])
                                    Modification Poulailler
                                @else
                                    Nouveau Poulailler
                                @endif


                            </div>
                            <div class="card-body card-block">
                                <form method="POST" enctype="multipart/form-data" {{-- action="{{ (isset($poulaillers) && $poulailler->exists) ? url('/Fournisseurs/add-edit/'.$poulaillers->id) : url('/Fournisseurs/add-edit') }}"> --}}
                                    action="{{ $poulailler->id ? url('/Poulaillers/add-edit/' . $poulailler->id) : url('/Poulaillers/add-edit') }}">
                                    @csrf
                                    <div class="form-group ">
                                        <label class=" form-control-label">Nom</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                            <input type="text" class="form-control" name="poul_nom" placeholder="Nom"
                                                @if ($poulailler['poul_nom']) value="{{ $poulailler['poul_nom'] }}"
											@else value="{{ old('poul_nom') }}" @endif>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <label class=" form-control-label">Capacité</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                            <input type="number" class="form-control" name="poul_capa"
                                                placeholder="Capacité"
                                                @if ($poulailler['poul_capa']) value="{{ $poulailler['poul_capa'] }}"
											@else value="{{ old('poul_capa') }}" @endif>
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <label class=" form-control-label">Emplacement</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-home"></i></div>
                                            <input type="text" class="form-control" name="poul_empl"
                                                placeholder="Emplacement"
                                                @if ($poulailler['poul_empl']) value="{{ $poulailler['poul_empl'] }}"
											@else value="{{ old('poul_empl') }}" @endif>
                                        </div>
                                    </div>

                                    {{-- <div class="form-group ">
									<label class=" form-control-label">Tel</label>
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-phone"></i></div>
										<input type="text" class="form-control" name="poul_tel" placeholder="Tel "
											@if ($poulailler['poul_tel']) value="{{ $poulailler['poul_tel'] }}"
											@else value="{{ old('poul_tel') }}"
											@endif>
									</div>
								</div> --}}

                                    @if ($poulailler->id)
                                        <div class="form-group">
                                            <label class="form-control-label">État du poulailler</label>
                                            <div class="input-group">
                                                <div class="radio-inline">
                                                    <input type="radio" name="poul_etat" value="0"
                                                        @if ($poulailler->poul_etat == 0) checked @endif>
                                                    <label class="ml-1">En service</label>

                                                    <input type="radio" name="poul_etat" value="1"
                                                        @if ($poulailler->poul_etat == 1) checked @endif class="ml-3">
                                                    <label class="ml-1 mr-1">En reparation</label>


                                                    <input type="radio" name="poul_etat" value="2"
                                                        @if ($poulailler->poul_etat == 2) checked @endif>
                                                    <label class="ml-1">Hors service</label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif





                                    <div class="modal-footer">
                                        <a href="{{ url('/Poulaillers') }}" class="btn btn-danger mb-1">
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
