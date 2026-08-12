@extends('layout.header')

<div id="right-panel" class="right-panel">
    <div class="content">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-lg-3"></div>
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        @if ($errors->any())
                            <div class="alert alert-danger mt-3 mx-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="card-header fw-bold bg-white"> 
                            @if ($ferme->id)
                                <i class="fa fa-edit"></i> Modifier ma Ferme 
                            @else 
                                <i class="fa fa-plus"></i> Créer une nouvelle Ferme
                            @endif
                        </div>

                        <div class="card-body card-block">
                            <form method="POST" action="{{ $ferme->id ? url('Fermes/add-edit/'.$ferme->id) : url('Fermes/add-edit') }}" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="form-group mb-3">
                                    <label class="form-control-label fw-bold">Nom de la Ferme</label>
                                    <div class="input-group">
                                        <div class="input-group-addon bg-light border-end-0"><i class="fa fa-university"></i></div>
                                        <input type="text" class="form-control" name="fer_nom" placeholder="Ex: Ferme SIDI OUMAR"
                                            value="{{ old('fer_nom', $ferme->fer_nom) }}" required>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-control-label fw-bold">Adresse / Localisation</label>
                                    <div class="input-group">
                                        <div class="input-group-addon bg-light border-end-0"><i class="fa fa-map-marker"></i></div>
                                        <input type="text" class="form-control" name="fer_adresse" placeholder="Adresse complète"
                                            value="{{ old('fer_adresse', $ferme->fer_adresse) }}">
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-control-label fw-bold">Adresse Email</label>
                                    <div class="input-group">
                                        <div class="input-group-addon bg-light border-end-0"><i class="fa fa-map-marker"></i></div>
                                        <input type="email" class="form-control" name="fer_email" placeholder="Adresse Email"
                                            value="{{ old('fer_email', $ferme->fer_email) }}">
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-control-label fw-bold">Téléphone de contact</label>
                                    <div class="input-group">
                                        <div class="input-group-addon bg-light border-end-0"><i class="fa fa-phone"></i></div>
                                        <input type="text" class="form-control" name="fer_telephone" placeholder="Ex: +223 00 00 00 00"
                                            value="{{ old('fer_telephone', $ferme->fer_telephone) }}" maxlength="8">
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-control-label fw-bold">Logo de la Ferme (pour les factures)</label>
                                    <div class="input-group">
                                        <div class="input-group-addon bg-light border-end-0"><i class="fa fa-picture-o"></i></div>
                                        <input type="file" class="form-control" name="fer_logo" accept="image/*">
                                    </div>
                                    @if($ferme->fer_logo)
                                        <div class="mt-2 text-center">
                                            <img src="{{ asset('storage/'.$ferme->fer_logo) }}" alt="Logo actuel" style="height: 60px; border-radius: 5px;">
                                            <p class="small text-muted">Logo actuel</p>
                                        </div>
                                    @endif
                                </div>

                                @if($ferme->id)
                                <div class="form-group mb-4">
                                    <label class="form-control-label fw-bold">État de l'établissement</label>
                                    <div class="d-flex align-items-center mt-1">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="fer_etat" id="actif" value="0" {{ !$ferme->fer_etat ? 'checked' : '' }}>
                                            <label class="form-check-label" for="actif">Opérationnel</label>
                                        </div>
                                        <div class="form-check form-check-inline ms-3">
                                            <input class="form-check-input" type="radio" name="fer_etat" id="inactif" value="1" {{ $ferme->fer_etat ? 'checked' : '' }}>
                                            <label class="form-check-label" for="inactif">Suspendu</label>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="modal-footer bg-light px-0 pb-0 pt-3">
                                    <a href="{{ url('/Fermes') }}" class="btn btn-secondary shadow-sm">
                                        <i class="fa fa-arrow-left"></i> Retour
                                    </a>
                                    @if ($ferme->id)
                                    <button type="submit" class="btn btn-primary shadow-sm">
                                        <i class="fa fa-save"></i> Enregistrer les modifications
                                    </button>
                                    @else                            
                                    <button type="submit" class="btn btn-primary shadow-sm">
                                        <i class="fa fa-save"></i> Valider l'ajoue
                                    </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>