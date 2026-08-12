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
                                @if ($matiere['id'])
                                    Modification Matieres
                                @else
                                    Nouveau Matieres
                                @endif


                            </div>
                            <div class="card-body card-block">
                                <form method="POST" enctype="multipart/form-data" {{-- action="{{ (isset($matieres) && $matiere->exists) ? url('/Fournisseurs/add-edit/'.$matieres->id) : url('/Fournisseurs/add-edit') }}"> --}}
                                    action="{{ $matiere->id ? url('/Matieres/add-edit/' . $matiere->id) : url('/Matieres/add-edit') }}">
                                    @csrf
                                    <div class="form-group ">
                                        <label class=" form-control-label">Nom</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                            <input type="text" class="form-control" name="ma_nom" placeholder="Nom"
                                                @if ($matiere['ma_nom']) value="{{ $matiere['ma_nom'] }}"
											@else value="{{ old('ma_nom') }}" @endif>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <label class=" form-control-label">Type</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                            <input type="text" class="form-control" name="ma_type" placeholder="Type"
                                                @if ($matiere['ma_type']) value="{{ $matiere['ma_type'] }}"
											@else value="{{ old('ma_type') }}" @endif>
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <label class=" form-control-label">Stock</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-home"></i></div>
                                            <input type="number" class="form-control" name="ma_stock" placeholder="Stock"
                                                @if ($matiere['ma_stock']) value="{{ $matiere['ma_stock'] }}"ma_
											@else value="{{ old('ma_stock') }}" @endif>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="{{ url('/Matieres') }}" class="btn btn-danger mb-1">
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
@endsection;