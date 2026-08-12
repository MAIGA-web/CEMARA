@extends('layout.header')
@section('contenu')
    <div id="right-panel" class="right-panel">
        <div class="content">
            <div class="animated fadeIn">
                <div class="row">

                    <div class="col-10">
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
                                @if ($produit['id'])
                                    Modification Produit
                                @else
                                    Nouveau Produit
                                @endif


                            </div>
                            <div class="card-body card-block">
                                <form method="POST" enctype="multipart/form-data" {{-- action="{{ (isset($produits) && $produit->exists) ? url('/Fournisseurs/add-edit/'.$produits->id) : url('/Fournisseurs/add-edit') }}"> --}}
                                    action="{{ $produit->id ? url('/Produits/add-edit/' . $produit->id) : url('/Produits/add-edit') }}">
                                    @csrf
                                    <div class="form-group ">
                                        <label class=" form-control-label">Nom</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                            <input type="text" class="form-control" name="pro_nom" placeholder="Nom"
                                                @if ($produit['pro_nom']) value="{{ $produit['pro_nom'] }}"
											@else value="{{ old('pro_nom') }}" @endif>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <label class=" form-control-label">Type</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                            <input type="text" class="form-control" name="pro_type" placeholder="Type"
                                                @if ($produit['pro_type']) value="{{ $produit['pro_type'] }}"
											@else value="{{ old('pro_type') }}" @endif>
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <label class=" form-control-label">Stock</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-home"></i></div>
                                            <input type="number" class="form-control" name="pro_stock" placeholder="Stock"
                                                @if ($produit['pro_stock']) value="{{ $produit['pro_stock'] }}"
											@else value="{{ old('pro_stock') }}" @endif>
                                        </div>
                                    </div>

                                    {{-- <div class="form-group ">
									<label class=" form-control-label">Tel</label>
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-phone"></i></div>
										<input type="text" class="form-control" name="pro_tel" placeholder="Tel "
											@if ($produit['pro_tel']) value="{{ $produit['pro_tel'] }}"
											@else value="{{ old('pro_tel') }}"
											@endif>
									</div>
								</div> --}}

                                    {{-- @if ($produit->id) --}}
                                    <div class="form-group">
                                        <label class="form-control-label">État du Produit</label>
                                        <div class="input-group">
                                            <div class="radio-inline">
                                                <input type="radio" name="pro_etat" value="1"
                                                    @if ($produit->pro_etat == 1) checked @endif>
                                                <label class="ml-1">Vendre (Poulles)</label><br>

                                                <input type="radio" name="pro_etat" value="3"
                                                    @if ($produit->pro_etat == 3) checked @endif>
                                                <label class="ml-1">Vendre (Ouef)</label><br>


                                                <input type="radio" name="pro_etat" value="0"
                                                    @if ($produit->pro_etat == 0) checked
											@else not @endif
                                                    class="ml-3">
                                                <label class="ml-1">Non vendre (Vaccin)</label><br>
                                                <input type="radio" name="pro_etat" value="2"
                                                    @if ($produit->pro_etat == 2) checked
											@else not @endif
                                                    class="ml-3">
                                                <label class="ml-1">Non vendre (Aliments)</label>


                                                {{-- <input type="radio" name="pro_etat" value="2" 										
										@if ($produit->pro_etat == 2)
										 checked
										 @endif>
                                        <label class="ml-1">Hors service</label> --}}
                                            </div>
                                        </div>
                                    </div>
                                    {{-- @else --}}
                                    {{-- <div class="form-group">
                                <label class="form-control-label">État du Produit</label>
                                <div class="input-group">
                                    <div class="radio-inline">
                                        <input type="radio" name="pro_etat" value="1" --}}
                                    {{-- @if ($produit->pro_etat == 1)
										 checked
										 @endif> --}}
                                    {{-- > --}}
                                    {{-- <label class="ml-1">Vendre</label>

                                        <input type="radio" name="pro_etat" value="0"  --}}
                                    {{-- @if ($produit->pro_etat == 0)
											checked
											@else not  --}}
                                    {{-- class="ml-3">
                                        <label class="ml-1 mr-1">Non vendre</label> --}}


                                    {{-- <input type="radio" name="pro_etat" value="2" 										
										@if ($produit->pro_etat == 2)
										 checked
										 @endif>
                                        <label class="ml-1">Hors service</label> --}}
                                    {{-- </div>
                                </div>
                            </div> --}}
                                    {{-- @endif --}}





                                    <div class="modal-footer">
                                        <a href="{{ url('/Produits') }}" class="btn btn-danger mb-1">
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
@endsection;
