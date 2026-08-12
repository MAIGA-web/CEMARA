@extends('layout.header')

<div id="right-panel" class="right-panel">
    <div class="content">
        <div class="animated fadeIn">
            <div class="row">

                <div class="col-lg-3"></div>
                <div class="col-lg-6">
                    <div class="card">
                        {{-- Affichage des erreurs de validation Laravel --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="card-header">
                            <strong>{{ $vaccination['id'] ? 'Modification Vaccination' : 'Nouvelle Vaccination' }}</strong>
                        </div>
                        
                        <div class="card-body card-block">
                          <form method="POST" action="{{ $vaccination['id'] ? url('/Vaccinations/add-edit/' . $vaccination['id']) : url('/Vaccinations/add-edit') }}">
    @csrf
                                
                                {{-- Input masqué pour la ferme actuelle si nécessaire --}}
                                <input type="hidden" name="fer_id" value="{{ session('fer_id') }}">
                                <input type="hidden" name="emp" value="{{ $vaccination['id'] ? 'U' : 'C' }}">
    {{-- Passe l'ID de la vaccination en cas de modification --}}
    <input type="hidden" name="vac_id" value="{{ $vaccination['id'] }}">

                                <!-- Champ Date -->
                                <div class="form-group">
                                    <label class="form-control-label">Date de Vaccination</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="date" class="form-control" name="vac_date" 
                                               value="{{ old('vac_date', $vaccination['vac_date'] ?? date('Y-m-d')) }}" required>
                                    </div>
                                </div>

                                <!-- Champ Quantité -->
                                <div class="form-group">
                                    <label class="form-control-label">Quantité (Flacons)</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-flask"></i></div>
                                        <input type="number" class="form-control" name="vac_qte" placeholder="Quantité" min="1"
                                               value="{{ old('vac_qte', $vaccination['vac_qte']) }}" required>
                                    </div>
                                </div>

                                <!-- Sélection du Vaccin (Produit) -->
                                <div class="form-group">
                                    <label class="form-control-label">Type de Vaccin</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-medkit"></i></div>
                                        <select name="pro_id" class="form-control" required>
                                            <option value="">Sélectionner le vaccin...</option>
                                            @foreach ($produits as $prod)
                                                <option value="{{ $prod['id'] }}" {{ old('pro_id', $vaccination['pro_id']) == $prod['id'] ? 'selected' : '' }}>
                                                    {{ $prod['pro_nom'] }} (Dispo: {{ $prod['pro_stock'] }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Sélection du Vétérinaire -->
                                <div class="form-group">
                                    <label class="form-control-label">Vétérinaire</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-user-md"></i></div>
                                        <select name="vtr_id" class="form-control" required>
                                            <option value="">Sélectionner le vétérinaire...</option>
                                            @foreach ($veterinaires as $veto)
                                                <option value="{{ $veto['id'] }}" {{ old('vtr_id', $vaccination['vtr_id']) == $veto['id'] ? 'selected' : '' }}>
                                                    {{ $veto['vtr_nom'] }} {{ $veto['vtr_prenom'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Sélection du Poulailler -->
                                <div class="form-group">
                                    <label class="form-control-label">Poulailler</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-home"></i></div>
                                        <select name="poul_id" class="form-control" required>
                                            <option value="">Sélectionner le poulailler...</option>
                                            @foreach ($poulaillers as $poul)
                                                <option value="{{ $poul['id'] }}" {{ old('poul_id', $vaccination['poul_id']) == $poul['id'] ? 'selected' : '' }}>
                                                    {{ $poul['poul_nom'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Footer du Formulaire -->
                                <div class="modal-footer">
                                   <a href="{{ url('/Vaccinations') }}" class="btn btn-danger mb-1">Annuler</a>
        <button type="submit" name="valider" value="Valider" class="btn btn-primary">Valider</button>
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