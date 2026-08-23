@extends('layout.header')

@section('contenu')
    <div class="content">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="card shadow-sm border-0">
                        
                        <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between">
                            <strong class="card-title text-white mb-0">
                                <i class="fa {{ $transformation->id ? 'fa-pencil-square' : 'fa-plus-circle' }} mr-2"></i>
                                {{ $transformation->id ? 'Modifier la Transformation n° ' . $transformation->id : 'Créer une nouvelle Transformation' }}
                            </strong>
                            <a href="{{ route('transformations.index') }}" class="btn btn-sm btn-outline-light">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                        </div>

                        <div class="card-body p-4">
                            @if (session('error_message'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fa fa-exclamation-triangle mr-2"></i> {{ session('error_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            {{-- Formulaire standard vertical (retrait de form-inline pour un joli rendu) --}}
                            <form action="{{ url('/Transformations/store') }}" method="POST">
                                @csrf

                                {{-- Identification de l'action pour le contrôleur --}}
                                <input type="hidden" name="emp" value="{{ $transformation->id ? 'U' : 'C' }}">
                                @if ($transformation->id)
                                    <input type="hidden" name="trans_id" value="{{ $transformation->id }}">
                                @endif

                                {{-- Champ 1 : Sélection de la Matière Première --}}
                                <div class="form-group mb-3">
                                    <label for="ma_id" class="font-weight-bold text-secondary">
                                        <i class="fa fa-flask mr-1"></i> Matière Première à transformer
                                    </label>
                                    <select name="ma_id" class="form-control custom-select" id="ma_id" required>
                                        <option value="">-- Choisir une matière --</option>
                                        @foreach ($matieres as $m)
                                            <option value="{{ $m->id }}"
                                                {{ old('ma_id', $transformation->ma_id ?? '') == $m->id ? 'selected' : '' }}>
                                                {{ $m->ma_nom }} (Stock act. : {{ number_format($m->ma_stock, 2, ',', ' ') }} Kg)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Champ 2 : Quantité à injecter (Correction du 'name' pour correspondre au Case 'U') --}}
                                <div class="form-group mb-3">
                                    <label for="trans_qte" class="font-weight-bold text-secondary">
                                        <i class="fa fa-balance-scale mr-1"></i> Quantité à transformer (Kg)
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0.01" name="trans_qte" id="trans_qte" 
                                            class="form-control" placeholder="Ex: 500.00"
                                            value="{{ old('trans_qte', $transformation->trans_qte) }}" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text bg-light font-weight-bold">Kg</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Champ 3 : Date Opération (Lecture seule lors de la modification) --}}
                                @if ($transformation->id)
                                    <div class="form-group mb-4">
                                        <label for="trans_date" class="font-weight-bold text-secondary">
                                            <i class="fa fa-calendar mr-1"></i> Date de l'opération
                                        </label>
                                        <input type="text" name="trans_date" id="trans_date" class="form-control bg-light" readonly
                                            value="{{ $transformation->created_at ? $transformation->created_at->format('d/m/Y à H:i') : now()->format('d/m/Y') }}">
                                    </div>
                                @endif

                                <hr class="my-4">

                                {{-- Actions du formulaire bien espacées --}}
                                <div class="d-flex justify-content-end align-items-center" style="gap: 10px;">
                                    <a href="{{ route('transformations.index') }}" class="btn btn-secondary px-4">
                                        Annuler
                                    </a>
                                    <button type="submit" class="btn {{ $transformation->id ? 'btn-warning text-white' : 'btn-primary' }} px-4">
                                        <i class="fa fa-save mr-1"></i> 
                                        {{ $transformation->id ? 'Enregistrer les modifications' : 'Lancer la transformation' }}
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection