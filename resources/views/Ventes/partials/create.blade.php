@extends('layout.header')

<div id="right-panel" class="right-panel">
    <div class="content">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-4"></div>
                <div class="col-8">
                    <div class="card col-6">
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
                            @if ($vendre->id)
                                Modification contenu vente
                            @else
                                Nouvelle contenu vente
                            @endif
                        </div>
                        <div class="card-body card-block">
                            <form method="POST" enctype="multipart/form-data"
                                action="{{ $vendre->id ? url('/Ventes/Vendre/vente/add-edit/' . $vendre->id) : url('/Ventes/Vendre/vente/add-edit/' . $vente->id) }}">
                                @csrf

                                {{-- On garde l'ID de la vente dans un champ caché --}}
                                <input type="hidden" name="vte_id" value="{{ $vente->id }}">
                                <input type="hidden" name="vte_id"
                                    value="{{ $vendre->vte_id ?? request()->query('vte_id') }}">
                                @csrf
                                <div class="form-group ">
                                    <label class=" form-control-label">Produit</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-user"></i>
                                        </div>
                                        <select id="selectLg" name="pro_id" class="form-control-lg form-control">
                                            <option selected>Selectionner</option>
                                            @foreach ($produit as $value_produit)
                                                <option value="{{ $value_produit->id }}"
                                                    {{ isset($vendre) && $vendre->pro_id == $value_produit->id ? 'selected' : '' }}>
                                                    {{ $value_produit->pro_nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label class=" form-control-label">Prix unitaire</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                        <input type="number" class="form-control" name="vdr_pu"
                                            placeholder="Prix unitaire"
                                            @if ($vendre['vdr_pu']) value="{{ $vendre['vdr_pu'] }}"
											@else value="{{ old('vdr_pu') }}" @endif>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label class=" form-control-label">Quantité</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                        <input type="text" class="form-control" name="vdr_qte" placeholder="Quantité"
                                            @if ($vendre['vdr_qte']) value="{{ $vendre['vdr_qte'] }}"
											@else value="{{ old('vdr_qte') }}" @endif>
                                    </div>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <a href="{{ url('/Ventes') }}" class="btn btn-danger mb-1">
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
