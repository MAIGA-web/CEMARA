@extends('layout.header')

@section('contenu')
    <div class="content">
        <div class="card col-md-6 offset-md-3">
            <div class="card-header">
                <strong>{{ $prd->id ? 'Modifier la Fiche Production n°' . $prd->id : 'Créer une nouvelle Production' }}</strong>
            </div>
            <div class="card-body">
                {{-- Action redirigée vers ta route unique storeAction --}}
                <form action="{{ route('production.action') }}" method="POST">
                    @csrf

                    {{-- Champs cachés indispensables pour ton contrôleur actuel (Cases C et U) --}}
                    <input type="hidden" name="emp" value="{{ $prd->id ? 'U' : 'C' }}">
                    @if ($prd->id)
                        <input type="hidden" name="prd_id" value="{{ $prd->id }}">
                    @endif

                    {{-- Champ 1 : Poulailler --}}
                    <div class="form-group">
                        <label for="poul_id">Poulailler</label>
                        <select name="poul_id" class="form-control" id="poul_id" required>
                            <option value="">-- Choisir un poulailler --</option>
                            @foreach ($poulailler as $l)
                                <option value="{{ $l->id }}"
                                    {{ old('poul_id', $prd->poul_id ?? '') == $l->id ? 'selected' : '' }}>
                                    {{ $l->poul_nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Champ 2 : Quantité d'œufs récoltés --}}
                    <div class="form-group">
                        <label for="nbr_ouef">Quantité Œufs par alvéole</label>
                        <input type="number" name="nbr_ouef" id="nbr_ouef" placeholder="Quantité Œuf" min="0"
                            max="{{ $max_oeuf_stock ?? '' }}" class="form-control"
                            value="{{ old('nbr_ouef', $prd->nbr_ouef) }}" required>
                    </div>

                    {{-- Champ 3 : Durée du cycle --}}
                    <div class="form-group">
                        <label for="prodc_dure">Durée (Jours)</label>
                        <input type="number" name="prodc_dure" id="prodc_dure" placeholder="Durée" min="0"
                            class="form-control" value="{{ old('prodc_dure', $prd->prodc_dure ?? 20) }}" required>
                    </div>

                    {{-- Champ 4 : Affichage optionnel de la date (Seulement lors de la modification) --}}
                    @if ($prd->id)
                        <div class="form-group">
                            <label for="prd_date">Date Production</label>
                            <input type="text" name="prd_date" id="prd_date" class="form-control"
                                value="{{ $prd->created_at ? $prd->created_at->format('d/m/Y') : now()->format('d/m/Y') }}">
                        </div>
                    @endif

                    {{-- Actions du formulaire --}}
                    <div class="mt-4 text-center">
                        <button type="submit" name="valider" value="Valider" class="btn btn-primary">
                            {{ $prd->id ? 'Mettre à jour' : 'Créer la production' }}
                        </button>
                        <a href="{{ route('production.index') }}" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
